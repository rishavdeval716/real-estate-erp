<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\HandoverCertificateModel;
use App\Models\BookingModel;
use App\Models\CustomerModel;
use App\Models\PropertyUnitModel;
use App\Models\CompanyModel;
use App\Models\AuditLogModel;

class HandoverController extends BaseController
{
    protected $handoverModel;
    protected $bookingModel;
    protected $customerModel;
    protected $unitModel;
    protected $companyModel;

    public function __construct()
    {
        $this->handoverModel = new HandoverCertificateModel();
        $this->bookingModel  = new BookingModel();
        $this->customerModel = new CustomerModel();
        $this->unitModel     = new PropertyUnitModel();
        $this->companyModel  = new CompanyModel();
    }

    public function index()
    {
        $handovers = $this->handoverModel->getHandoversWithDetails();

        $data = [
            'title'     => 'Possession & Key Handover Management',
            'handovers' => $handovers,
        ];

        return view('handover/index', $data);
    }

    public function create()
    {
        // Get confirmed bookings that have not been handed over yet
        $db = \Config\Database::connect();
        $existingHandoverBookingIds = $db->table('handover_certificates')->select('booking_id')->get()->getResultArray();
        $excludeIds = array_column($existingHandoverBookingIds, 'booking_id');

        $builder = $db->table('bookings')
            ->select('bookings.id, bookings.booking_number, bookings.customer_id, bookings.property_unit_id, customers.first_name, customers.last_name, property_units.unit_number')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->whereIn('bookings.booking_status', ['Confirmed', 'Completed']);

        if (!empty($excludeIds)) {
            $builder->whereNotIn('bookings.id', $excludeIds);
        }

        $eligibleBookings = $builder->get()->getResultArray();

        $data = [
            'title'    => 'Execute Unit Possession & Key Handover',
            'bookings' => $eligibleBookings,
            'today'    => date('Y-m-d'),
        ];

        return view('handover/create', $data);
    }

    public function store()
    {
        $rules = [
            'booking_id'    => 'required|numeric',
            'handover_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $bookingId = (int)$this->request->getPost('booking_id');
        $booking   = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return redirect()->back()->withInput()->with('error', 'Booking not found.');
        }

        $code   = $this->handoverModel->generateCertificateNumber();
        $userId = session()->get('user_id') ?: 1;

        $handoverData = [
            'certificate_number'          => $code,
            'booking_id'                  => $bookingId,
            'customer_id'                 => $booking['customer_id'],
            'property_unit_id'            => $booking['property_unit_id'],
            'handover_date'               => $this->request->getPost('handover_date'),
            'financial_clearance'         => (int)($this->request->getPost('financial_clearance') ?? 1),
            'snagging_clearance'          => (int)($this->request->getPost('snagging_clearance') ?? 1),
            'occupancy_certificate_ref'   => trim($this->request->getPost('occupancy_certificate_ref') ?? ''),
            'electricity_meter_number'    => trim($this->request->getPost('electricity_meter_number') ?? ''),
            'initial_electricity_reading' => (float)$this->request->getPost('initial_electricity_reading') ?: 0.00,
            'water_meter_number'          => trim($this->request->getPost('water_meter_number') ?? ''),
            'initial_water_reading'       => (float)$this->request->getPost('initial_water_reading') ?: 0.00,
            'key_sets_provided'           => (int)$this->request->getPost('key_sets_provided') ?: 2,
            'customer_acknowledged'       => (int)($this->request->getPost('customer_acknowledged') ?? 1),
            'authorized_by'               => $userId,
            'status'                      => 'Handed Over',
            'notes'                       => trim($this->request->getPost('notes') ?? ''),
        ];

        $insertedId = $this->handoverModel->insert($handoverData);

        // Update Unit availability status to 'Sold' or 'Occupied'
        if ($booking['property_unit_id']) {
            $this->unitModel->update($booking['property_unit_id'], [
                'availability_status' => 'Sold',
            ]);
        }

        AuditLogModel::record(
            'POSSESSION_HANDOVER_COMPLETED',
            'Handover',
            $insertedId,
            "Completed possession handover {$code} for Booking {$booking['booking_number']}"
        );

        return redirect()->to("/handover/certificate/{$insertedId}")->with('success', "Handover {$code} executed successfully.");
    }

    public function certificate(int $id)
    {
        $handover = $this->handoverModel->getHandoverById($id);
        if (!$handover) {
            return redirect()->to('/handover')->with('error', 'Handover certificate not found.');
        }

        $company = $this->companyModel->first();

        $data = [
            'title'    => "Possession Handover Certificate - {$handover['certificate_number']}",
            'handover' => $handover,
            'company'  => $company,
        ];

        return view('handover/certificate', $data);
    }
}
