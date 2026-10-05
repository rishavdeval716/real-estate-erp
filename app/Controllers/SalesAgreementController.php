<?php

namespace App\Controllers;

use App\Models\SalesAgreementModel;
use App\Models\BookingModel;
use App\Models\CompanyModel;
use App\Models\AuditLogModel;

class SalesAgreementController extends BaseController
{
    protected SalesAgreementModel $agreementModel;
    protected BookingModel $bookingModel;

    public function __construct()
    {
        $this->agreementModel = new SalesAgreementModel();
        $this->bookingModel   = new BookingModel();
    }

    /**
     * List sales agreements
     */
    public function index()
    {
        $filters = [
            'search'           => trim((string)$this->request->getGet('search')),
            'agreement_status' => trim((string)$this->request->getGet('agreement_status')),
            'sort'             => $this->request->getGet('sort') ?? 'sales_agreements.created_at',
            'order'            => $this->request->getGet('order') ?? 'DESC',
        ];

        $result = $this->agreementModel->getAgreementsWithDetails($filters, 15);

        return view('agreements/index', [
            'title'      => 'Sales Agreements',
            'agreements' => $result['agreements'],
            'pager'      => $result['pager'],
            'filters'    => $filters,
        ]);
    }

    /**
     * Create agreement form
     */
    public function create()
    {
        $bookingId = (int)($this->request->getGet('booking_id') ?? 0);
        $selectedBooking = null;

        if ($bookingId > 0) {
            $selectedBooking = $this->bookingModel->getBookingWithFullDetails($bookingId);
        }

        // Available confirmed/draft bookings without an active agreement
        $bookings = $this->bookingModel->select('bookings.*, customers.first_name, customers.last_name, property_units.unit_number')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->whereIn('bookings.booking_status', ['Draft', 'Pending Confirmation', 'Confirmed'])
            ->where('bookings.deleted_at', null)
            ->orderBy('bookings.id', 'DESC')
            ->findAll();

        return view('agreements/create', [
            'title'           => 'Generate Sales Agreement',
            'bookingId'       => $bookingId,
            'selectedBooking' => $selectedBooking,
            'bookings'        => $bookings,
            'defaultTerms'    => SalesAgreementModel::getDefaultTerms(),
        ]);
    }

    /**
     * Store new agreement
     */
    public function store()
    {
        $rules = [
            'booking_id'         => 'required|is_not_unique[bookings.id]',
            'agreement_date'     => 'required|valid_date',
            'agreement_type'     => 'required|in_list[Agreement to Sale,Sale Deed,Allotment Letter,Tripartite Agreement]',
            'total_value'        => 'required|numeric|greater_than[0]',
            'terms_conditions'   => 'required',
            'special_conditions' => 'permit_empty|max_length[2000]',
            'remarks'            => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookingId = (int)$this->request->getPost('booking_id');
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking not found.');
        }

        $agreementNumber = $this->agreementModel->generateAgreementNumber();
        $userId = session()->get('user_id');

        $data = [
            'agreement_number'   => $agreementNumber,
            'booking_id'         => $bookingId,
            'customer_id'        => $booking['customer_id'],
            'project_id'         => $booking['project_id'],
            'property_id'        => $booking['property_id'],
            'property_unit_id'   => $booking['property_unit_id'],
            'agreement_date'     => $this->request->getPost('agreement_date'),
            'agreement_type'     => $this->request->getPost('agreement_type'),
            'agreement_status'   => 'Draft',
            'total_value'        => (float)$this->request->getPost('total_value'),
            'terms_conditions'   => $this->request->getPost('terms_conditions'),
            'special_conditions' => $this->request->getPost('special_conditions'),
            'remarks'            => $this->request->getPost('remarks'),
            'created_by'         => $userId,
        ];

        $agreementId = $this->agreementModel->insert($data);

        AuditLogModel::record(
            'Agreement Created',
            'sales_agreements',
            $agreementId,
            "Created Sales Agreement {$agreementNumber} for Booking #{$booking['booking_number']} (Value: {$data['total_value']})",
            $userId
        );

        return redirect()->to(base_url("agreements/view/{$agreementId}"))
            ->with('success', "Sales Agreement {$agreementNumber} generated successfully.");
    }

    /**
     * View agreement document / printable preview
     */
    public function view(int $id)
    {
        $agreement = $this->agreementModel->select('sales_agreements.*, 
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
            customers.id_proof_type,
            customers.id_proof_number,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            property_units.floor,
            property_units.carpet_area,
            property_units.built_up_area,
            projects.name as project_name,
            users.name as creator_name')
            ->join('bookings', 'bookings.id = sales_agreements.booking_id', 'left')
            ->join('customers', 'customers.id = sales_agreements.customer_id', 'left')
            ->join('properties', 'properties.id = sales_agreements.property_id', 'left')
            ->join('property_units', 'property_units.id = sales_agreements.property_unit_id', 'left')
            ->join('projects', 'projects.id = sales_agreements.project_id', 'left')
            ->join('users', 'users.id = sales_agreements.created_by', 'left')
            ->where('sales_agreements.id', $id)
            ->first();

        if (!$agreement) {
            return redirect()->to(base_url('agreements'))->with('error', 'Sales agreement not found.');
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'sales@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Plot 101, Business Park, Tech Zone, Mumbai, Maharashtra 400001',
        ];

        return view('agreements/view', [
            'title'     => "Agreement {$agreement['agreement_number']}",
            'agreement' => $agreement,
            'company'   => $company,
        ]);
    }

    /**
     * Mark agreement as Signed
     */
    public function sign(int $id)
    {
        $agreement = $this->agreementModel->find($id);
        if (!$agreement) {
            return redirect()->to(base_url('agreements'))->with('error', 'Agreement not found.');
        }

        if ($agreement['agreement_status'] === 'Signed') {
            return redirect()->back()->with('error', 'This agreement is already marked as Signed.');
        }

        $userId = session()->get('user_id');
        $this->agreementModel->update($id, [
            'agreement_status' => 'Signed',
        ]);

        AuditLogModel::record(
            'Agreement Signed',
            'sales_agreements',
            $id,
            "Agreement {$agreement['agreement_number']} marked as Signed.",
            $userId
        );

        return redirect()->to(base_url("agreements/view/{$id}"))
            ->with('success', "Agreement {$agreement['agreement_number']} marked as Signed.");
    }

    /**
     * Cancel agreement
     */
    public function cancel(int $id)
    {
        $agreement = $this->agreementModel->find($id);
        if (!$agreement) {
            return redirect()->to(base_url('agreements'))->with('error', 'Agreement not found.');
        }

        $userId = session()->get('user_id');
        $this->agreementModel->update($id, [
            'agreement_status' => 'Cancelled',
        ]);

        AuditLogModel::record(
            'Agreement Cancelled',
            'sales_agreements',
            $id,
            "Agreement {$agreement['agreement_number']} cancelled.",
            $userId
        );

        return redirect()->to(base_url("agreements/view/{$id}"))
            ->with('success', "Agreement {$agreement['agreement_number']} marked as Cancelled.");
    }
}
