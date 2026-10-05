<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PortalRequestModel;
use App\Models\CustomerModel;
use App\Models\BookingModel;
use App\Models\SalesAgreementModel;
use App\Models\PaymentModel;
use App\Models\ReceiptModel;
use App\Models\InvoiceModel;
use App\Models\TenantModel;
use App\Models\LeaseAgreementModel;
use App\Models\SecurityDepositModel;
use App\Models\RentDemandModel;
use App\Models\RentCollectionModel;
use App\Models\MaintenanceRequestModel;
use App\Models\ComplaintModel;
use App\Models\CommissionModel;
use App\Models\TdsEntryModel;
use App\Models\LeadModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class PortalController extends BaseController
{
    protected $requestModel;
    protected $auditModel;

    public function __construct()
    {
        $this->requestModel = new PortalRequestModel();
        $this->auditModel   = new AuditLogModel();
    }

    /**
     * Owner / Property Buyer Portal
     */
    public function owner()
    {
        $userId = session()->get('user_id');
        $userEmail = session()->get('user_email');
        $isSuperAdmin = session()->get('is_super_admin') ?? false;

        $customerModel  = new CustomerModel();
        $bookingModel   = new BookingModel();
        $agreementModel = new SalesAgreementModel();
        $paymentModel   = new PaymentModel();
        $receiptModel   = new ReceiptModel();
        $invoiceModel   = new InvoiceModel();

        // Find customer associated with this user
        $customer = $customerModel->where('email', $userEmail)->where('deleted_at', null)->first();

        // If admin or customer found, load data
        $customerId = $customer['id'] ?? ($isSuperAdmin ? 1 : null);

        $bookings   = [];
        $agreements = [];
        $receipts   = [];
        $invoices   = [];

        if ($customerId) {
            $bookings = $bookingModel->select('bookings.*, p.title as property_title, u.unit_number')
                ->join('properties p', 'p.id = bookings.property_id')
                ->join('property_units u', 'u.id = bookings.property_unit_id')
                ->where('bookings.customer_id', $customerId)
                ->where('bookings.deleted_at', null)
                ->findAll();

            $agreements = $agreementModel->where('customer_id', $customerId)->findAll();
            $receipts   = $receiptModel->select('receipts.*, b.booking_number')
                ->join('bookings b', 'b.id = receipts.booking_id')
                ->where('receipts.customer_id', $customerId)
                ->findAll();

            $invoices   = $invoiceModel->where('customer_id', $customerId)->findAll();
        }

        // 2. Also check if authenticated user is registered as a Property Landlord/Owner
        $ownerModel = new \App\Models\PropertyOwnerModel();
        $db = \Config\Database::connect();
        $ownerProfile = $ownerModel->where('email', $userEmail)->orWhere('user_id', $userId)->where('deleted_at', null)->first();
        if (!$ownerProfile && $isSuperAdmin) {
            $ownerProfile = $ownerModel->where('deleted_at', null)->first();
        }

        $ownedProperties   = [];
        $tenantLeases      = [];
        $rentalCollections = [];
        $maintenanceTickets= [];
        $totalRentalIncome = 0.00;

        if ($ownerProfile) {
            $ownedProperties = $db->table('properties')
                ->select('properties.*, property_types.name as property_type_name, locations.city as location_city')
                ->join('property_types', 'property_types.id = properties.property_type_id', 'left')
                ->join('locations', 'locations.id = properties.location_id', 'left')
                ->where('properties.owner_id', $ownerProfile['id'])
                ->where('properties.deleted_at', null)
                ->get()->getResultArray();

            $propIds = array_column($ownedProperties, 'id');
            if (!empty($propIds)) {
                $tenantLeases = $db->table('lease_agreements')
                    ->select('lease_agreements.*, tenants.full_name as tenant_fname, tenants.mobile as tenant_phone, property_units.unit_number, properties.title as property_title')
                    ->join('tenants', 'tenants.id = lease_agreements.tenant_id', 'left')
                    ->join('property_units', 'property_units.id = lease_agreements.property_unit_id', 'left')
                    ->join('properties', 'properties.id = property_units.property_id', 'left')
                    ->whereIn('property_units.property_id', $propIds)
                    ->where('lease_agreements.deleted_at', null)
                    ->get()->getResultArray();

                $rentalCollections = $db->table('rent_collections')
                    ->select('rent_collections.*, tenants.full_name as tenant_fname, rent_demands.demand_number')
                    ->join('rent_demands', 'rent_demands.id = rent_collections.rent_demand_id', 'left')
                    ->join('lease_agreements', 'lease_agreements.id = rent_demands.lease_id', 'left')
                    ->join('property_units', 'property_units.id = lease_agreements.property_unit_id', 'left')
                    ->join('tenants', 'tenants.id = rent_collections.tenant_id', 'left')
                    ->whereIn('property_units.property_id', $propIds)
                    ->orderBy('rent_collections.payment_date', 'DESC')
                    ->get()->getResultArray();

                $totalRentalIncome = array_sum(array_column($rentalCollections, 'amount'));

                $maintenanceTickets = $db->table('maintenance_requests')
                    ->select('maintenance_requests.*, properties.title as property_title, property_units.unit_number')
                    ->join('properties', 'properties.id = maintenance_requests.property_id', 'left')
                    ->join('property_units', 'property_units.id = maintenance_requests.property_unit_id', 'left')
                    ->whereIn('maintenance_requests.property_id', $propIds)
                    ->orderBy('maintenance_requests.id', 'DESC')
                    ->get()->getResultArray();
            }
        }

        $myRequests = $this->requestModel->where('portal_type', 'customer')->where('user_id', $userId)->findAll();

        return view('portal/owner', [
            'title'              => 'Customer & Owner Portal - Real Estate ERP',
            'customer'           => $customer,
            'bookings'           => $bookings,
            'agreements'         => $agreements,
            'receipts'           => $receipts,
            'invoices'           => $invoices,
            'requests'           => $myRequests,
            'ownerProfile'       => $ownerProfile,
            'ownedProperties'    => $ownedProperties,
            'tenantLeases'       => $tenantLeases,
            'rentalCollections'  => $rentalCollections,
            'maintenanceTickets' => $maintenanceTickets,
            'totalRentalIncome'  => $totalRentalIncome,
        ]);
    }

    /**
     * Tenant Resident Portal
     */
    public function tenant()
    {
        $userId = session()->get('user_id');
        $userEmail = session()->get('user_email');
        $isSuperAdmin = session()->get('is_super_admin') ?? false;

        $tenantModel      = new TenantModel();
        $leaseModel       = new LeaseAgreementModel();
        $depositModel     = new SecurityDepositModel();
        $demandModel      = new RentDemandModel();
        $collectionModel  = new RentCollectionModel();
        $ticketModel      = new MaintenanceRequestModel();
        $complaintModel   = new ComplaintModel();

        // Match tenant
        $tenant = $tenantModel->where('email', $userEmail)->where('deleted_at', null)->first();
        $tenantId = $tenant['id'] ?? ($isSuperAdmin ? 1 : null);

        $leases      = [];
        $deposits    = [];
        $demands     = [];
        $collections = [];
        $tickets     = [];
        $complaints  = [];

        if ($tenantId) {
            $leases = $leaseModel->select('lease_agreements.*, p.title as property_title, u.unit_number')
                ->join('properties p', 'p.id = lease_agreements.property_id')
                ->join('property_units u', 'u.id = lease_agreements.property_unit_id', 'left')
                ->where('lease_agreements.tenant_id', $tenantId)
                ->findAll();

            $deposits = $depositModel->where('tenant_id', $tenantId)->findAll();
            $demands  = $demandModel->where('tenant_id', $tenantId)->orderBy('due_date', 'DESC')->findAll();
            $collections = $collectionModel->where('tenant_id', $tenantId)->findAll();
            $tickets     = $ticketModel->where('tenant_id', $tenantId)->findAll();
            $complaints  = $complaintModel->where('tenant_id', $tenantId)->findAll();
        }

        $myRequests = $this->requestModel->where('portal_type', 'tenant')->where('user_id', $userId)->findAll();

        return view('portal/tenant', [
            'title'       => 'Tenant Portal - Real Estate ERP',
            'tenant'      => $tenant,
            'leases'      => $leases,
            'deposits'    => $deposits,
            'demands'     => $demands,
            'collections' => $collections,
            'tickets'     => $tickets,
            'complaints'  => $complaints,
            'requests'    => $myRequests,
        ]);
    }

    /**
     * Channel Partner / Agent Portal
     */
    public function partner()
    {
        $userId = session()->get('user_id');
        $userEmail = session()->get('user_email');
        $isSuperAdmin = session()->get('is_super_admin') ?? false;

        $leadModel       = new LeadModel();
        $commissionModel = new CommissionModel();
        $tdsModel        = new TdsEntryModel();
        $unitModel       = new PropertyUnitModel();

        // Leads referred or assigned
        $leads = $leadModel->select('leads.*, ls.name as source_name')
            ->join('lead_sources ls', 'ls.id = leads.lead_source_id', 'left')
            ->where('leads.deleted_at', null)
            ->limit(20)
            ->findAll();

        $commissions = $commissionModel->select('commissions.*, b.booking_number, c.first_name, c.last_name')
            ->join('bookings b', 'b.id = commissions.booking_id')
            ->join('customers c', 'c.id = b.customer_id')
            ->findAll();

        $tdsEntries = $tdsModel->findAll();

        $availableUnits = $unitModel->select('property_units.*, p.title as property_title, pt.name as property_type_name')
            ->join('properties p', 'p.id = property_units.property_id')
            ->join('property_types pt', 'pt.id = p.property_type_id', 'left')
            ->where('property_units.availability_status', 'Available')
            ->limit(15)
            ->findAll();

        $myRequests = $this->requestModel->where('portal_type', 'partner')->where('user_id', $userId)->findAll();

        return view('portal/partner', [
            'title'          => 'Channel Partner Portal - Real Estate ERP',
            'leads'          => $leads,
            'commissions'    => $commissions,
            'tdsEntries'     => $tdsEntries,
            'availableUnits' => $availableUnits,
            'requests'       => $myRequests,
        ]);
    }

    /**
     * Submit Self-Service Portal Request
     */
    public function submitRequest()
    {
        $rules = [
            'portal_type'  => 'required|in_list[customer,tenant,partner]',
            'request_type' => 'required|max_length[100]',
            'subject'      => 'required|min_length[3]|max_length[255]',
            'details'      => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code   = $this->requestModel->generateRequestCode();
        $userId = session()->get('user_id');

        $data = [
            'request_code' => $code,
            'portal_type'  => $this->request->getPost('portal_type'),
            'user_id'      => $userId,
            'request_type' => trim($this->request->getPost('request_type')),
            'subject'      => trim($this->request->getPost('subject')),
            'details'      => trim($this->request->getPost('details')),
            'status'       => 'submitted',
        ];

        $reqId = $this->requestModel->insert($data);

        $this->auditModel->record(
            $userId,
            'PORTAL_REQUEST_SUBMITTED',
            'Portal',
            $reqId,
            "Submitted {$data['portal_type']} portal request {$code} ({$data['subject']})"
        );

        return redirect()->back()->with('success', "Request {$code} submitted successfully. An operations officer will review shortly.");
    }
}
