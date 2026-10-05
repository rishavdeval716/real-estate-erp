<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaseAgreementModel extends Model
{
    protected $table            = 'lease_agreements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'agreement_number',
        'tenant_id',
        'property_id',
        'property_unit_id',
        'agreement_type',
        'start_date',
        'end_date',
        'lock_in_period_months',
        'notice_period_days',
        'monthly_rent',
        'security_deposit',
        'maintenance_charges',
        'rent_escalation_pct',
        'escalation_frequency',
        'payment_due_day',
        'late_fee_amount',
        'terms_conditions',
        'document_path',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Generate sequential unique agreement number: LSE-2026-000001
     */
    public function generateAgreementNumber(): string
    {
        $year = date('Y');
        $prefix = "LSE-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('agreement_number');
        $builder->like('agreement_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['agreement_number'])) {
            $parts = explode('-', $last['agreement_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return sprintf('%s%06d', $prefix, $nextSeq);
    }

    /**
     * Get single lease agreement with all related details
     */
    public function getLeaseWithDetails(int $id): ?array
    {
        return $this->select('lease_agreements.*, t.full_name as tenant_name, t.tenant_code, t.mobile as tenant_mobile, t.email as tenant_email, t.company_name as tenant_company, p.title as property_title, p.property_code, p.title as property_address, u.unit_number, u.floor, pt.name as property_type_name, u_created.name as creator_name')
            ->join('tenants t', 't.id = lease_agreements.tenant_id')
            ->join('properties p', 'p.id = lease_agreements.property_id')
            ->join('property_units u', 'u.id = lease_agreements.property_unit_id', 'left')
            ->join('property_types pt', 'pt.id = p.property_type_id', 'left')
            ->join('users u_created', 'u_created.id = lease_agreements.created_by', 'left')
            ->where('lease_agreements.id', $id)
            ->first();
    }

    /**
     * Update statuses of expiring and expired leases
     */
    public function updateExpiringStatus(): void
    {
        $today = date('Y-m-d');
        $soonThreshold = date('Y-m-d', strtotime('+30 days'));

        // Mark expired
        $this->where('status', 'active')
            ->where('end_date <', $today)
            ->set(['status' => 'expired'])
            ->update();

        // Mark expiring soon
        $this->where('status', 'active')
            ->where('end_date >=', $today)
            ->where('end_date <=', $soonThreshold)
            ->set(['status' => 'expiring_soon'])
            ->update();
    }
}
