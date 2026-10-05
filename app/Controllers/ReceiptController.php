<?php

namespace App\Controllers;

use App\Models\ReceiptModel;
use App\Models\CompanyModel;

class ReceiptController extends BaseController
{
    protected ReceiptModel $receiptModel;

    public function __construct()
    {
        $this->receiptModel = new ReceiptModel();
    }

    /**
     * List all payment receipts
     */
    public function index()
    {
        $filters = [
            'search'         => trim((string)$this->request->getGet('search')),
            'payment_method' => trim((string)$this->request->getGet('payment_method')),
            'booking_id'     => $this->request->getGet('booking_id'),
            'sort'           => $this->request->getGet('sort') ?? 'receipts.receipt_date',
            'order'          => $this->request->getGet('order') ?? 'DESC',
        ];

        $result = $this->receiptModel->getReceiptsWithDetails($filters, 15);

        return view('receipts/index', [
            'title'    => 'Official Receipts',
            'receipts' => $result['receipts'],
            'pager'    => $result['pager'],
            'filters'  => $filters,
        ]);
    }

    /**
     * View / Print official receipt
     */
    public function view(int $id)
    {
        $receipt = $this->receiptModel->select('receipts.*, 
            payments.payment_number,
            payments.bank_name,
            payments.cheque_number,
            bookings.booking_number,
            bookings.booking_date,
            customers.customer_code,
            customers.first_name,
            customers.last_name,
            customers.phone as customer_phone,
            customers.address as customer_address,
            customers.city as customer_city,
            properties.title as property_title,
            property_units.unit_number,
            projects.name as project_name')
            ->join('payments', 'payments.id = receipts.payment_id', 'left')
            ->join('bookings', 'bookings.id = receipts.booking_id', 'left')
            ->join('customers', 'customers.id = receipts.customer_id', 'left')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('projects', 'projects.id = bookings.project_id', 'left')
            ->where('receipts.id', $id)
            ->first();

        if (!$receipt) {
            return redirect()->to(base_url('receipts'))->with('error', 'Receipt record not found.');
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'finance@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Plot 101, Business Park, Tech Zone, Mumbai, Maharashtra 400001',
        ];

        return view('receipts/view', [
            'title'   => "Receipt {$receipt['receipt_number']}",
            'receipt' => $receipt,
            'company' => $company,
        ]);
    }
}
