<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesAgreementModel extends Model
{
    protected $table            = 'sales_agreements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'agreement_number',
        'booking_id',
        'customer_id',
        'project_id',
        'property_id',
        'property_unit_id',
        'agreement_date',
        'agreement_type',
        'agreement_status',
        'total_value',
        'terms_conditions',
        'special_conditions',
        'remarks',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate sequential unique agreement number: AGR-2026-000001
     */
    public function generateAgreementNumber(): string
    {
        $year = date('Y');
        $prefix = "AGR-{$year}-";

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
     * Get agreements with joined details
     */
    public function getAgreementsWithDetails(array $filters = [], int $perPage = 15): array
    {
        $builder = $this->select('sales_agreements.*, 
            bookings.booking_number,
            customers.customer_code, 
            customers.first_name, 
            customers.last_name, 
            customers.phone as customer_phone,
            properties.title as property_title,
            property_units.unit_number,
            projects.name as project_name')
            ->join('bookings', 'bookings.id = sales_agreements.booking_id', 'left')
            ->join('customers', 'customers.id = sales_agreements.customer_id', 'left')
            ->join('properties', 'properties.id = sales_agreements.property_id', 'left')
            ->join('property_units', 'property_units.id = sales_agreements.property_unit_id', 'left')
            ->join('projects', 'projects.id = sales_agreements.project_id', 'left');

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('sales_agreements.agreement_number', $s)
                ->orLike('bookings.booking_number', $s)
                ->orLike('customers.first_name', $s)
                ->orLike('customers.last_name', $s)
                ->orLike('property_units.unit_number', $s)
                ->groupEnd();
        }

        if (!empty($filters['agreement_status'])) {
            $builder->where('sales_agreements.agreement_status', $filters['agreement_status']);
        }

        $sort = $filters['sort'] ?? 'sales_agreements.created_at';
        $order = $filters['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $agreements = $builder->paginate($perPage);

        return [
            'agreements' => $agreements,
            'pager'      => $this->pager,
        ];
    }

    /**
     * Standard editable template clauses
     */
    public static function getDefaultTerms(): string
    {
        return "1. SALE AND CONVEYANCE: The Promoter/Seller agrees to sell and the Allottee/Purchaser agrees to purchase the designated unit as described in the Schedule of Property.\n" .
               "2. CONSIDERATION & PAYMENT SCHEDULE: The total agreed consideration shall be paid by the Purchaser strictly in accordance with the agreed construction-linked milestone plan.\n" .
               "3. POSSESSION & HANDOVER: The Seller shall complete construction and deliver peaceful possession subject to timely milestone clearances and force majeure circumstances.\n" .
               "4. DEFAULT & INTEREST: Delay in milestone payments beyond due date shall attract simple interest at the prescribed statutory rate until cleared.\n" .
               "5. CANCELLATION: In the event of voluntary cancellation, applicable statutory and administrative deductions shall apply before processing refund.\n" .
               "6. JURISDICTION: This agreement is executed in accordance with applicable Real Estate Regulatory Authority regulations.";
    }
}
