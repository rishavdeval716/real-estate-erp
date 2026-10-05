<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\BookingStatusHistoryModel;
use App\Models\CustomerModel;
use App\Models\PropertyUnitModel;
use App\Models\ProjectModel;
use App\Models\PropertyModel;
use App\Models\UserModel;
use App\Models\UnitHoldModel;
use App\Models\LeadModel;
use App\Models\PaymentScheduleModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class BookingController extends BaseController
{
    protected BookingModel $bookingModel;
    protected BookingStatusHistoryModel $historyModel;
    protected CustomerModel $customerModel;
    protected PropertyUnitModel $unitModel;
    protected ProjectModel $projectModel;
    protected PropertyModel $propertyModel;
    protected UserModel $userModel;
    protected UnitHoldModel $unitHoldModel;
    protected LeadModel $leadModel;
    protected PaymentScheduleModel $scheduleModel;

    public function __construct()
    {
        $this->bookingModel   = new BookingModel();
        $this->historyModel   = new BookingStatusHistoryModel();
        $this->customerModel  = new CustomerModel();
        $this->unitModel      = new PropertyUnitModel();
        $this->projectModel   = new ProjectModel();
        $this->propertyModel  = new PropertyModel();
        $this->userModel      = new UserModel();
        $this->unitHoldModel  = new UnitHoldModel();
        $this->leadModel      = new LeadModel();
        $this->scheduleModel  = new PaymentScheduleModel();
    }

    /**
     * List bookings with search and filters
     */
    public function index()
    {
        $filters = [
            'search'             => trim((string)$this->request->getGet('search')),
            'booking_status'     => trim((string)$this->request->getGet('booking_status')),
            'project_id'         => $this->request->getGet('project_id'),
            'property_id'        => $this->request->getGet('property_id'),
            'sales_executive_id' => $this->request->getGet('sales_executive_id'),
            'date_from'          => $this->request->getGet('date_from'),
            'date_to'            => $this->request->getGet('date_to'),
            'sort'               => $this->request->getGet('sort') ?? 'bookings.created_at',
            'order'              => $this->request->getGet('order') ?? 'DESC',
        ];

        $page = (int)($this->request->getGet('page') ?? 1);
        $result = $this->bookingModel->getBookingsWithDetails($filters, 15);

        // Fetch filter dropdown options
        $projects   = $this->projectModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $properties = $this->propertyModel->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();
        $executives = $this->userModel->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'      => 'Booking Management',
            'bookings'   => $result['bookings'],
            'pager'      => $result['pager'],
            'filters'    => $filters,
            'projects'   => $projects,
            'properties' => $properties,
            'executives' => $executives,
        ];

        return view('bookings/index', $data);
    }

    /**
     * Form to create a new booking
     */
    public function create()
    {
        $selectedUnitId = (int)($this->request->getGet('unit_id') ?? 0);
        $selectedLeadId = (int)($this->request->getGet('lead_id') ?? 0);
        $selectedHoldId = (int)($this->request->getGet('hold_id') ?? 0);
        $selectedCustomerId = (int)($this->request->getGet('customer_id') ?? 0);

        $selectedUnit = null;
        if ($selectedUnitId > 0) {
            $selectedUnit = $this->unitModel->select('property_units.*, properties.title as property_title, projects.name as project_name')
                ->join('properties', 'properties.id = property_units.property_id', 'left')
                ->join('projects', 'projects.id = property_units.project_id', 'left')
                ->where('property_units.id', $selectedUnitId)
                ->first();
        }

        // If a lead was selected but no customer yet, check if lead is already converted
        if ($selectedLeadId > 0 && $selectedCustomerId === 0) {
            $existingCustomer = $this->customerModel->where('lead_id', $selectedLeadId)->first();
            if ($existingCustomer) {
                $selectedCustomerId = (int)$existingCustomer['id'];
            }
        }

        $customers  = $this->customerModel->where('deleted_at', null)->orderBy('first_name', 'ASC')->findAll();
        $projects   = $this->projectModel->where('deleted_at', null)->orderBy('name', 'ASC')->findAll();
        $properties = $this->propertyModel->where('deleted_at', null)->orderBy('title', 'ASC')->findAll();
        $executives = $this->userModel->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        
        // Available units
        $units = $this->unitModel->select('property_units.*, properties.title as property_title, projects.name as project_name')
            ->join('properties', 'properties.id = property_units.property_id', 'left')
            ->join('projects', 'projects.id = property_units.project_id', 'left')
            ->whereIn('property_units.availability_status', ['Available', 'Reserved'])
            ->where('property_units.deleted_at', null)
            ->orderBy('property_units.unit_number', 'ASC')
            ->findAll();

        return view('bookings/create', [
            'title'              => 'Create New Booking',
            'customers'          => $customers,
            'projects'           => $projects,
            'properties'         => $properties,
            'executives'         => $executives,
            'units'              => $units,
            'selectedUnitId'     => $selectedUnitId,
            'selectedUnit'       => $selectedUnit,
            'selectedCustomerId' => $selectedCustomerId,
            'selectedLeadId'     => $selectedLeadId,
            'selectedHoldId'     => $selectedHoldId,
        ]);
    }

    /**
     * Store new booking
     */
    public function store()
    {
        $rules = [
            'customer_id'        => 'required|is_not_unique[customers.id]',
            'property_unit_id'   => 'required|is_not_unique[property_units.id]',
            'booking_date'       => 'required|valid_date',
            'base_price'         => 'required|numeric|greater_than[0]',
            'discount'           => 'permit_empty|numeric|greater_than_equal_to[0]',
            'tax_amount'         => 'permit_empty|numeric|greater_than_equal_to[0]',
            'token_amount'       => 'permit_empty|numeric|greater_than_equal_to[0]',
            'booking_amount'     => 'permit_empty|numeric|greater_than_equal_to[0]',
            'sales_executive_id' => 'permit_empty|is_not_unique[users.id]',
            'remarks'            => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unitId = (int)$this->request->getPost('property_unit_id');
        $customerId = (int)$this->request->getPost('customer_id');

        // 1. Verify unit exists and availability
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->back()->withInput()->with('error', 'Selected property unit was not found.');
        }

        // 2. Prevent duplicate active booking
        if ($this->bookingModel->hasActiveBookingForUnit($unitId)) {
            return redirect()->back()->withInput()->with('error', 'Selected unit already has an active booking. Duplicate bookings are not permitted.');
        }

        // 3. Unit status must be Available or Reserved
        if (!in_array($unit['availability_status'], ['Available', 'Reserved'])) {
            return redirect()->back()->withInput()->with('error', "Unit cannot be booked because its current status is '{$unit['availability_status']}'.");
        }

        // 4. Check unit hold if Reserved
        $customer = $this->customerModel->find($customerId);
        $leadId = $customer['lead_id'] ? (int)$customer['lead_id'] : null;

        $activeHold = $this->unitHoldModel->where('property_unit_id', $unitId)
            ->where('hold_status', 'Active')
            ->first();

        if ($activeHold && $leadId && $activeHold['lead_id'] != $leadId) {
            return redirect()->back()->withInput()->with('error', 'This unit is currently held by a different prospect. Hold must be released before booking.');
        }

        $basePrice     = (float)$this->request->getPost('base_price');
        $discount      = (float)($this->request->getPost('discount') ?: 0);
        $taxAmount     = (float)($this->request->getPost('tax_amount') ?: 0);
        $tokenAmount   = (float)($this->request->getPost('token_amount') ?: 0);
        $bookingAmount = (float)($this->request->getPost('booking_amount') ?: 0);
        $finalAmount   = max(0, $basePrice - $discount + $taxAmount);

        $bookingNumber = $this->bookingModel->generateBookingNumber();
        $userId        = session()->get('user_id');

        $bookingData = [
            'booking_number'     => $bookingNumber,
            'customer_id'        => $customerId,
            'lead_id'            => $leadId,
            'project_id'         => $unit['project_id'],
            'property_id'        => $unit['property_id'] ?: null,
            'property_unit_id'   => $unitId,
            'sales_executive_id' => $this->request->getPost('sales_executive_id') ?: $userId,
            'booking_date'       => $this->request->getPost('booking_date'),
            'booking_status'     => 'Draft',
            'base_price'         => $basePrice,
            'discount'           => $discount,
            'tax_amount'         => $taxAmount,
            'final_amount'       => $finalAmount,
            'token_amount'       => $tokenAmount,
            'booking_amount'     => $bookingAmount,
            'remarks'            => $this->request->getPost('remarks'),
            'created_by'         => $userId,
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $bookingId = $this->bookingModel->insert($bookingData);

        // Record status history
        $this->historyModel->recordChange($bookingId, null, 'Draft', $userId, 'Initial booking created in Draft status.');

        // Auto-generate standard payment schedule
        $scheduleId = $this->scheduleModel->generateStandardMilestones(
            $bookingId,
            $finalAmount,
            $this->request->getPost('booking_date')
        );

        // If hold existed, mark converted
        if ($activeHold) {
            $this->unitHoldModel->update($activeHold['id'], [
                'hold_status' => 'Converted',
                'released_at' => date('Y-m-d H:i:s'),
                'remarks'     => "Converted to Booking #{$bookingNumber}",
            ]);
        }

        // Audit log
        AuditLogModel::record(
            'Booking Created',
            'bookings',
            $bookingId,
            "Created Booking {$bookingNumber} for customer {$customer['first_name']} {$customer['last_name']} on Unit #{$unit['unit_number']} (Total: {$finalAmount})",
            $userId
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to create booking due to a database error.');
        }

        return redirect()->to(base_url("bookings/view/{$bookingId}"))
            ->with('success', "Booking {$bookingNumber} created successfully. Payment schedule generated.");
    }

    /**
     * View detailed booking file with tabs for schedule, payments, invoices, agreements, status
     */
    public function view(int $id)
    {
        $booking = $this->bookingModel->getBookingWithFullDetails($id);
        if (!$booking) {
            return redirect()->to(base_url('bookings'))->with('error', 'Booking record not found.');
        }

        // Users for commission assignment
        $agents = $this->userModel->where('deleted_at', null)->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        
        $db = \Config\Database::connect();
        $commissionRules = $db->table('commission_rules')->where('status', 'Active')->get()->getResultArray();

        return view('bookings/view', [
            'title'           => "Booking {$booking['booking_number']}",
            'booking'         => $booking,
            'agents'          => $agents,
            'commissionRules' => $commissionRules,
        ]);
    }

    /**
     * Confirm a draft or pending booking -> transitions Unit to 'Booked'
     */
    public function confirm(int $id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->to(base_url('bookings'))->with('error', 'Booking not found.');
        }

        if ($booking['booking_status'] === 'Confirmed') {
            return redirect()->back()->with('error', 'This booking is already confirmed.');
        }

        if ($booking['booking_status'] === 'Cancelled') {
            return redirect()->back()->with('error', 'Cancelled bookings cannot be confirmed.');
        }

        $unitId = (int)$booking['property_unit_id'];
        $unit   = $this->unitModel->find($unitId);
        $userId = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        $oldStatus = $booking['booking_status'];

        // 1. Update Booking Status
        $this->bookingModel->update($id, [
            'booking_status' => 'Confirmed',
        ]);

        // 2. Transition Unit Status to 'Booked'
        $oldUnitStatus = $unit['availability_status'];
        $this->unitModel->update($unitId, [
            'availability_status' => 'Booked',
        ]);

        // 3. Record Property Status History
        $propStatusHist = new PropertyStatusHistoryModel();
        $propStatusHist->recordChange(
            (int)($unit['property_id'] ?: 0),
            $unitId,
            $oldUnitStatus,
            'Booked',
            $userId,
            "Unit transitioned to Booked upon confirmation of Booking #{$booking['booking_number']}"
        );

        // 4. Record Booking Status History
        $this->historyModel->recordChange(
            $id,
            $oldStatus,
            'Confirmed',
            $userId,
            'Booking formally confirmed. Unit locked as Booked.'
        );

        // 5. If booking has associated CRM lead, mark lead Won
        if (!empty($booking['lead_id'])) {
            $this->leadModel->update($booking['lead_id'], [
                'status' => 'Won',
            ]);
        }

        // 6. Audit Log
        AuditLogModel::record(
            'Booking Confirmed',
            'bookings',
            $id,
            "Confirmed Booking {$booking['booking_number']}. Unit status updated to Booked.",
            $userId
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to confirm booking due to database error.');
        }

        return redirect()->to(base_url("bookings/view/{$id}"))
            ->with('success', "Booking {$booking['booking_number']} has been successfully Confirmed. Unit status transitioned to Booked.");
    }

    /**
     * Cancel an active booking -> releases Unit back to 'Available'
     */
    public function cancel(int $id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->to(base_url('bookings'))->with('error', 'Booking not found.');
        }

        if ($booking['booking_status'] === 'Cancelled') {
            return redirect()->back()->with('error', 'This booking is already cancelled.');
        }

        $reason = trim((string)$this->request->getPost('cancellation_reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'A formal cancellation reason is required.');
        }

        $unitId = (int)$booking['property_unit_id'];
        $unit   = $this->unitModel->find($unitId);
        $userId = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        $oldStatus = $booking['booking_status'];

        // 1. Update Booking status
        $this->bookingModel->update($id, [
            'booking_status' => 'Cancelled',
            'remarks'        => ($booking['remarks'] ? $booking['remarks'] . "\n" : '') . "[Cancelled: " . date('Y-m-d H:i') . "] Reason: " . $reason,
        ]);

        // 2. Restore Unit status to 'Available'
        $oldUnitStatus = $unit ? $unit['availability_status'] : 'Booked';
        if ($unit) {
            $this->unitModel->update($unitId, [
                'availability_status' => 'Available',
            ]);

            // Record Property Status History
            $propStatusHist = new PropertyStatusHistoryModel();
            $propStatusHist->recordChange(
                (int)($unit['property_id'] ?: 0),
                $unitId,
                $oldUnitStatus,
                'Available',
                $userId,
                "Unit released to Available following cancellation of Booking #{$booking['booking_number']}. Reason: {$reason}"
            );
        }

        // 3. Record Booking Status History
        $this->historyModel->recordChange(
            $id,
            $oldStatus,
            'Cancelled',
            $userId,
            "Cancelled by user. Reason: {$reason}"
        );

        // 4. Audit Log
        AuditLogModel::record(
            'Booking Cancelled',
            'bookings',
            $id,
            "Cancelled Booking {$booking['booking_number']}. Unit #{$unitId} released back to Available. Reason: {$reason}",
            $userId
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to cancel booking due to database error.');
        }

        return redirect()->to(base_url("bookings/view/{$id}"))
            ->with('success', "Booking {$booking['booking_number']} has been Cancelled. Unit released to Available.");
    }

    /**
     * Printable booking voucher / confirmation document
     */
    public function voucher(int $id)
    {
        $booking = $this->bookingModel->getBookingWithFullDetails($id);
        if (!$booking) {
            return redirect()->to(base_url('bookings'))->with('error', 'Booking not found.');
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'sales@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Plot 101, Business Park, Tech Zone, Mumbai, Maharashtra 400001',
        ];

        return view('bookings/voucher', [
            'title'   => "Booking Voucher - {$booking['booking_number']}",
            'booking' => $booking,
            'company' => $company,
        ]);
    }
}
