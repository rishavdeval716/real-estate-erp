<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeaseAgreementModel;
use App\Models\SecurityDepositModel;
use App\Models\RentalHistoryModel;
use App\Models\RentDemandModel;
use App\Models\TenantModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;
use App\Models\CompanyModel;

class LeaseAgreementController extends BaseController
{
    protected $leaseModel;
    protected $depositModel;
    protected $historyModel;
    protected $demandModel;
    protected $tenantModel;
    protected $propertyModel;
    protected $unitModel;
    protected $statusHistoryModel;
    protected $auditModel;
    protected $companyModel;

    public function __construct()
    {
        $this->leaseModel         = new LeaseAgreementModel();
        $this->depositModel       = new SecurityDepositModel();
        $this->historyModel       = new RentalHistoryModel();
        $this->demandModel        = new RentDemandModel();
        $this->tenantModel        = new TenantModel();
        $this->propertyModel      = new PropertyModel();
        $this->unitModel          = new PropertyUnitModel();
        $this->statusHistoryModel = new PropertyStatusHistoryModel();
        $this->auditModel         = new AuditLogModel();
        $this->companyModel       = new CompanyModel();
    }

    public function index()
    {
        // Update statuses for overdue / expiring soon
        $this->leaseModel->updateExpiringStatus();

        $search = trim($this->request->getGet('search') ?? '');
        $status = trim($this->request->getGet('status') ?? '');
        $type   = trim($this->request->getGet('agreement_type') ?? '');

        $builder = $this->leaseModel->select('lease_agreements.*, t.full_name as tenant_name, t.tenant_code, p.title as property_title, u.unit_number')
            ->join('tenants t', 't.id = lease_agreements.tenant_id')
            ->join('properties p', 'p.id = lease_agreements.property_id')
            ->join('property_units u', 'u.id = lease_agreements.property_unit_id', 'left')
            ->where('lease_agreements.deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('lease_agreements.agreement_number', $search)
                ->orLike('t.full_name', $search)
                ->orLike('t.tenant_code', $search)
                ->orLike('p.title', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('lease_agreements.status', $status);
        }

        if ($type !== '') {
            $builder->where('lease_agreements.agreement_type', $type);
        }

        $leases = $builder->orderBy('lease_agreements.id', 'DESC')->paginate(15);
        $pager  = $this->leaseModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'         => $db->table('lease_agreements')->where('deleted_at', null)->countAllResults(),
            'active'        => $db->table('lease_agreements')->where('status', 'active')->where('deleted_at', null)->countAllResults(),
            'expiring_soon' => $db->table('lease_agreements')->where('status', 'expiring_soon')->where('deleted_at', null)->countAllResults(),
            'expired'       => $db->table('lease_agreements')->where('status', 'expired')->where('deleted_at', null)->countAllResults(),
        ];

        return view('agreements/lease_index', [
            'title'   => 'Lease Agreements - Real Estate ERP',
            'leases'  => $leases,
            'pager'   => $pager,
            'filters' => [
                'search'         => $search,
                'status'         => $status,
                'agreement_type' => $type,
            ],
            'kpi'     => $kpi,
        ]);
    }

    public function create()
    {
        $tenants    = $this->tenantModel->where('status', 'active')->where('deleted_at', null)->findAll();
        $properties = $this->propertyModel->findAll();
        $units      = $this->unitModel->where('availability_status', 'Available')->findAll();

        return view('agreements/lease_create', [
            'title'      => 'Create Lease Agreement - Real Estate ERP',
            'tenants'    => $tenants,
            'properties' => $properties,
            'units'      => $units,
        ]);
    }

    public function store()
    {
        $rules = [
            'tenant_id'             => 'required|is_natural_no_zero',
            'property_id'           => 'required|is_natural_no_zero',
            'start_date'            => 'required|valid_date',
            'end_date'              => 'required|valid_date',
            'monthly_rent'          => 'required|numeric|greater_than[0]',
            'security_deposit'      => 'permit_empty|numeric',
            'rent_escalation_pct'   => 'permit_empty|numeric',
            'lock_in_period_months' => 'permit_empty|integer',
            'notice_period_days'    => 'permit_empty|integer',
            'payment_due_day'       => 'permit_empty|integer|greater_than[0]|less_than_equal_to[31]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $number = $this->leaseModel->generateAgreementNumber();
        $userId = session()->get('user_id');

        $data = [
            'agreement_number'      => $number,
            'tenant_id'             => (int)$this->request->getPost('tenant_id'),
            'property_id'           => (int)$this->request->getPost('property_id'),
            'property_unit_id'      => $this->request->getPost('property_unit_id') ? (int)$this->request->getPost('property_unit_id') : null,
            'agreement_type'        => $this->request->getPost('agreement_type') ?? 'residential',
            'start_date'            => $this->request->getPost('start_date'),
            'end_date'              => $this->request->getPost('end_date'),
            'lock_in_period_months' => (int)($this->request->getPost('lock_in_period_months') ?? 0),
            'notice_period_days'    => (int)($this->request->getPost('notice_period_days') ?? 30),
            'monthly_rent'          => (float)$this->request->getPost('monthly_rent'),
            'security_deposit'      => (float)($this->request->getPost('security_deposit') ?? 0),
            'maintenance_charges'   => (float)($this->request->getPost('maintenance_charges') ?? 0),
            'rent_escalation_pct'   => (float)($this->request->getPost('rent_escalation_pct') ?? 5.0),
            'escalation_frequency'  => $this->request->getPost('escalation_frequency') ?? 'annual',
            'payment_due_day'       => (int)($this->request->getPost('payment_due_day') ?? 5),
            'late_fee_amount'       => (float)($this->request->getPost('late_fee_amount') ?? 0),
            'terms_conditions'      => trim($this->request->getPost('terms_conditions') ?? '') ?: null,
            'status'                => 'draft',
            'created_by'            => $userId,
        ];

        $leaseId = $this->leaseModel->insert($data);

        // If security deposit > 0, automatically initialize security_deposits entry
        if ($data['security_deposit'] > 0) {
            $depNumber = $this->depositModel->generateDepositNumber();
            $this->depositModel->insert([
                'deposit_number'    => $depNumber,
                'tenant_id'         => $data['tenant_id'],
                'lease_id'          => $leaseId,
                'amount'            => $data['security_deposit'],
                'deposit_date'      => $data['start_date'],
                'refundable_amount' => $data['security_deposit'],
                'adjusted_amount'   => 0.00,
                'refund_status'     => 'held',
                'remarks'           => "Security deposit for Lease Agreement {$number}",
            ]);
        }

        $this->auditModel->record(
            $userId,
            'LEASE_CREATED',
            'Leases',
            $leaseId,
            "Created Lease Agreement {$number} for Tenant #{$data['tenant_id']}"
        );

        return redirect()->to(base_url("leases/view/{$leaseId}"))->with('success', "Lease Agreement {$number} created.");
    }

    public function view(int $id)
    {
        $lease = $this->leaseModel->getLeaseWithDetails($id);
        if (!$lease) {
            return redirect()->to(base_url('leases'))->with('error', 'Lease agreement not found.');
        }

        $deposit = $this->depositModel->where('lease_id', $id)->first();
        $demands = $this->demandModel->where('lease_id', $id)->orderBy('due_date', 'DESC')->findAll();
        $history = $this->historyModel->where('lease_id', $id)->findAll();

        return view('agreements/lease_view', [
            'title'   => "Lease Agreement {$lease['agreement_number']}",
            'lease'   => $lease,
            'deposit' => $deposit,
            'demands' => $demands,
            'history' => $history,
        ]);
    }

    public function voucher(int $id)
    {
        $lease = $this->leaseModel->getLeaseWithDetails($id);
        if (!$lease) {
            return redirect()->to(base_url('leases'))->with('error', 'Lease not found.');
        }

        $company = $this->companyModel->first() ?? [
            'name'    => 'Real Estate ERP Enterprise Ltd.',
            'email'   => 'info@realestate-erp.local',
            'phone'   => '+91 98765 43210',
            'address' => 'Corporate Tower, Financial District',
            'city'    => 'Mumbai',
            'state'   => 'Maharashtra',
        ];

        return view('agreements/lease_voucher', [
            'title'   => "Lease Agreement Document - {$lease['agreement_number']}",
            'lease'   => $lease,
            'company' => $company,
        ]);
    }

    public function activate(int $id)
    {
        $lease = $this->leaseModel->find($id);
        if (!$lease) {
            return redirect()->to(base_url('leases'))->with('error', 'Lease not found.');
        }

        if ($lease['status'] === 'active') {
            return redirect()->back()->with('error', 'Lease is already active.');
        }

        $userId = session()->get('user_id');

        $this->leaseModel->update($id, ['status' => 'active']);

        // Update unit status to 'Rented'
        if (!empty($lease['property_unit_id'])) {
            $unit = $this->unitModel->find($lease['property_unit_id']);
            if ($unit) {
                $oldStatus = $unit['availability_status'];
                $this->unitModel->update($lease['property_unit_id'], ['availability_status' => 'Rented']);
                $this->statusHistoryModel->recordChange(
                    (int)($lease['property_id']),
                    (int)($lease['property_unit_id']),
                    $oldStatus,
                    'Rented',
                    $userId,
                    "Unit transitioned to Rented upon activation of Lease Agreement #{$lease['agreement_number']}"
                );
            }
        }

        // Update tenant occupancy info
        $this->tenantModel->update($lease['tenant_id'], [
            'occupied_property_id' => $lease['property_id'],
            'occupied_unit_id'     => $lease['property_unit_id'],
            'lease_start_date'     => $lease['start_date'],
            'lease_end_date'       => $lease['end_date'],
            'status'               => 'active',
        ]);

        // Record rental history
        $this->historyModel->insert([
            'tenant_id'        => $lease['tenant_id'],
            'property_id'      => $lease['property_id'],
            'property_unit_id' => $lease['property_unit_id'],
            'lease_id'         => $id,
            'previous_rent'    => null,
            'current_rent'     => $lease['monthly_rent'],
            'start_date'       => $lease['start_date'],
            'end_date'         => $lease['end_date'],
            'status'           => 'Active',
            'notes'            => "Lease {$lease['agreement_number']} activated.",
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->record(
            $userId,
            'LEASE_ACTIVATED',
            'Leases',
            $id,
            "Activated Lease Agreement {$lease['agreement_number']} for Tenant #{$lease['tenant_id']}"
        );

        return redirect()->back()->with('success', "Lease Agreement {$lease['agreement_number']} activated. Property Unit marked as Rented.");
    }

    public function terminate(int $id)
    {
        $lease = $this->leaseModel->find($id);
        if (!$lease) {
            return redirect()->to(base_url('leases'))->with('error', 'Lease not found.');
        }

        $userId = session()->get('user_id');

        $this->leaseModel->update($id, ['status' => 'terminated']);

        // Restore unit status to 'Available'
        if (!empty($lease['property_unit_id'])) {
            $unit = $this->unitModel->find($lease['property_unit_id']);
            if ($unit) {
                $oldStatus = $unit['availability_status'];
                $this->unitModel->update($lease['property_unit_id'], ['availability_status' => 'Available']);
                $this->statusHistoryModel->recordChange(
                    (int)($lease['property_id']),
                    (int)($lease['property_unit_id']),
                    $oldStatus,
                    'Available',
                    $userId,
                    "Unit restored to Available upon termination of Lease Agreement #{$lease['agreement_number']}"
                );
            }
        }

        // Update tenant occupancy info
        $this->tenantModel->update($lease['tenant_id'], [
            'occupied_unit_id' => null,
            'status'           => 'inactive',
        ]);

        $this->auditModel->record(
            $userId,
            'LEASE_TERMINATED',
            'Leases',
            $id,
            "Terminated Lease Agreement {$lease['agreement_number']}"
        );

        return redirect()->back()->with('success', "Lease Agreement {$lease['agreement_number']} terminated. Unit restored to Available.");
    }

    public function renew(int $id)
    {
        $oldLease = $this->leaseModel->find($id);
        if (!$oldLease) {
            return redirect()->to(base_url('leases'))->with('error', 'Lease not found.');
        }

        $escalationPct = (float)($oldLease['rent_escalation_pct'] ?? 5.0);
        $newRent       = round((float)$oldLease['monthly_rent'] * (1 + ($escalationPct / 100)), 2);

        // New dates: 1 year from old end_date
        $newStartDate = date('Y-m-d', strtotime($oldLease['end_date'] . ' +1 day'));
        $newEndDate   = date('Y-m-d', strtotime($newStartDate . ' +1 year -1 day'));

        $newNumber = $this->leaseModel->generateAgreementNumber();
        $userId    = session()->get('user_id');

        $newData = [
            'agreement_number'      => $newNumber,
            'tenant_id'             => $oldLease['tenant_id'],
            'property_id'           => $oldLease['property_id'],
            'property_unit_id'      => $oldLease['property_unit_id'],
            'agreement_type'        => $oldLease['agreement_type'],
            'start_date'            => $newStartDate,
            'end_date'              => $newEndDate,
            'lock_in_period_months' => $oldLease['lock_in_period_months'],
            'notice_period_days'    => $oldLease['notice_period_days'],
            'monthly_rent'          => $newRent,
            'security_deposit'      => $oldLease['security_deposit'],
            'maintenance_charges'   => $oldLease['maintenance_charges'],
            'rent_escalation_pct'   => $escalationPct,
            'escalation_frequency'  => $oldLease['escalation_frequency'],
            'payment_due_day'       => $oldLease['payment_due_day'],
            'late_fee_amount'       => $oldLease['late_fee_amount'],
            'terms_conditions'      => $oldLease['terms_conditions'],
            'status'                => 'active',
            'created_by'            => $userId,
        ];

        $newLeaseId = $this->leaseModel->insert($newData);

        // Mark old lease renewed
        $this->leaseModel->update($id, ['status' => 'renewed']);

        // Record rental history
        $this->historyModel->insert([
            'tenant_id'        => $oldLease['tenant_id'],
            'property_id'      => $oldLease['property_id'],
            'property_unit_id' => $oldLease['property_unit_id'],
            'lease_id'         => $newLeaseId,
            'previous_rent'    => $oldLease['monthly_rent'],
            'current_rent'     => $newRent,
            'start_date'       => $newStartDate,
            'end_date'         => $newEndDate,
            'status'           => 'Renewed',
            'notes'            => "Lease renewed from {$oldLease['agreement_number']} with {$escalationPct}% escalation.",
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->record(
            $userId,
            'LEASE_RENEWED',
            'Leases',
            $newLeaseId,
            "Renewed Lease {$oldLease['agreement_number']} -> New Lease {$newNumber} (Escalated rent: ₹{$newRent})"
        );

        return redirect()->to(base_url("leases/view/{$newLeaseId}"))->with('success', "Lease renewed successfully as {$newNumber} with escalated rent ₹{$newRent}.");
    }
}
