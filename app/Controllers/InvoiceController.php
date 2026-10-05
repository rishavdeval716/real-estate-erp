<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\BookingModel;
use App\Models\CompanyModel;
use App\Models\AuditLogModel;

class InvoiceController extends BaseController
{
    protected InvoiceModel $invoiceModel;
    protected BookingModel $bookingModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * List invoices
     */
    public function index()
    {
        $filters = [
            'search'     => trim((string)$this->request->getGet('search')),
            'status'     => trim((string)$this->request->getGet('status')),
            'booking_id' => $this->request->getGet('booking_id'),
            'sort'       => $this->request->getGet('sort') ?? 'invoices.invoice_date',
            'order'      => $this->request->getGet('order') ?? 'DESC',
        ];

        $result = $this->invoiceModel->getInvoicesWithDetails($filters, 15);

        return view('invoices/index', [
            'title'    => 'Invoices',
            'invoices' => $result['invoices'],
            'pager'    => $result['pager'],
            'filters'  => $filters,
        ]);
    }

    /**
     * Create invoice form
     */
    public function create()
    {
        $bookingId = (int)($this->request->getGet('booking_id') ?? 0);
        $selectedBooking = null;

        if ($bookingId > 0) {
            $selectedBooking = $this->bookingModel->getBookingWithFullDetails($bookingId);
        }

        $bookings = $this->bookingModel->select('bookings.*, customers.first_name, customers.last_name, property_units.unit_number')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->whereIn('bookings.booking_status', ['Draft', 'Pending Confirmation', 'Confirmed'])
            ->where('bookings.deleted_at', null)
            ->orderBy('bookings.id', 'DESC')
            ->findAll();

        return view('invoices/create', [
            'title'           => 'Generate Invoice',
            'bookingId'       => $bookingId,
            'selectedBooking' => $selectedBooking,
            'bookings'        => $bookings,
        ]);
    }

    /**
     * Store new invoice
     */
    public function store()
    {
        $rules = [
            'booking_id'   => 'required|is_not_unique[bookings.id]',
            'invoice_date' => 'required|valid_date',
            'due_date'     => 'required|valid_date',
            'subtotal'     => 'required|numeric|greater_than[0]',
            'discount'     => 'permit_empty|numeric|greater_than_equal_to[0]',
            'tax'          => 'permit_empty|numeric|greater_than_equal_to[0]',
            'status'       => 'required|in_list[Draft,Issued,Partially Paid,Paid,Cancelled,Overdue]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookingId = (int)$this->request->getPost('booking_id');
        $booking   = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking record not found.');
        }

        $subtotal = (float)$this->request->getPost('subtotal');
        $discount = (float)($this->request->getPost('discount') ?: 0);
        $tax      = (float)($this->request->getPost('tax') ?: 0);
        $total    = max(0, $subtotal - $discount + $tax);

        $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();
        $userId        = session()->get('user_id');

        $data = [
            'invoice_number' => $invoiceNumber,
            'booking_id'     => $bookingId,
            'customer_id'    => $booking['customer_id'],
            'invoice_date'   => $this->request->getPost('invoice_date'),
            'due_date'       => $this->request->getPost('due_date'),
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total_amount'   => $total,
            'paid_amount'    => 0.00,
            'balance_amount' => $total,
            'status'         => $this->request->getPost('status'),
        ];

        $invoiceId = $this->invoiceModel->insert($data);

        AuditLogModel::record(
            'Invoice Created',
            'invoices',
            $invoiceId,
            "Created Invoice {$invoiceNumber} for Booking #{$booking['booking_number']} (Total: ₹" . number_format($total, 2) . ")",
            $userId
        );

        return redirect()->to(base_url("invoices/view/{$invoiceId}"))
            ->with('success', "Invoice {$invoiceNumber} generated successfully.");
    }

    /**
     * View / Print invoice
     */
    public function view(int $id)
    {
        $invoice = $this->invoiceModel->select('invoices.*, 
            bookings.booking_number,
            bookings.booking_date,
            customers.customer_code,
            customers.first_name,
            customers.last_name,
            customers.email as customer_email,
            customers.phone as customer_phone,
            customers.address as customer_address,
            customers.city as customer_city,
            customers.state as customer_state,
            customers.pincode as customer_pincode,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            projects.name as project_name')
            ->join('bookings', 'bookings.id = invoices.booking_id', 'left')
            ->join('customers', 'customers.id = invoices.customer_id', 'left')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('projects', 'projects.id = bookings.project_id', 'left')
            ->where('invoices.id', $id)
            ->first();

        if (!$invoice) {
            return redirect()->to(base_url('invoices'))->with('error', 'Invoice not found.');
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'finance@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Plot 101, Business Park, Tech Zone, Mumbai, Maharashtra 400001',
        ];

        return view('invoices/view', [
            'title'   => "Invoice {$invoice['invoice_number']}",
            'invoice' => $invoice,
            'company' => $company,
        ]);
    }

    /**
     * Cancel an invoice
     */
    public function cancel(int $id)
    {
        $invoice = $this->invoiceModel->find($id);
        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $userId = session()->get('user_id');
        $this->invoiceModel->update($id, [
            'status' => 'Cancelled',
        ]);

        AuditLogModel::record(
            'Invoice Cancelled',
            'invoices',
            $id,
            "Invoice {$invoice['invoice_number']} cancelled.",
            $userId
        );

        return redirect()->back()->with('success', "Invoice {$invoice['invoice_number']} has been cancelled.");
    }
}
