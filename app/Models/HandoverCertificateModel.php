<?php

namespace App\Models;

use CodeIgniter\Model;

class HandoverCertificateModel extends Model
{
    protected $table            = 'handover_certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'certificate_number',
        'booking_id',
        'customer_id',
        'property_unit_id',
        'handover_date',
        'financial_clearance',
        'snagging_clearance',
        'occupancy_certificate_ref',
        'electricity_meter_number',
        'initial_electricity_reading',
        'water_meter_number',
        'initial_water_reading',
        'key_sets_provided',
        'customer_acknowledged',
        'authorized_by',
        'status',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateCertificateNumber(): string
    {
        $year = date('Y');
        $prefix = "HND-{$year}-";

        $db = \Config\Database::connect();
        $builder = $db->table($this->table)->select('certificate_number');
        $builder->like('certificate_number', $prefix, 'after');
        $builder->orderBy('id', 'DESC')->limit(1);
        $last = $builder->get()->getRowArray();

        $nextSeq = 1;
        if (!empty($last['certificate_number'])) {
            $parts = explode('-', $last['certificate_number']);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $nextSeq = (int)$parts[2] + 1;
            }
        }

        return $prefix . str_pad((string)$nextSeq, 6, '0', STR_PAD_LEFT);
    }

    public function getHandoversWithDetails(?int $bookingId = null)
    {
        $builder = $this->builder();
        $builder->select('handover_certificates.*, bookings.booking_number, customers.first_name, customers.last_name, customers.email, customers.phone, property_units.unit_number, projects.name as project_name, project_towers.tower_name, users.name as authorizer_name');
        $builder->join('bookings', 'bookings.id = handover_certificates.booking_id', 'left');
        $builder->join('customers', 'customers.id = handover_certificates.customer_id', 'left');
        $builder->join('property_units', 'property_units.id = handover_certificates.property_unit_id', 'left');
        $builder->join('projects', 'projects.id = property_units.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = property_units.tower_id', 'left');
        $builder->join('users', 'users.id = handover_certificates.authorized_by', 'left');

        if ($bookingId) {
            $builder->where('handover_certificates.booking_id', $bookingId);
        }

        $builder->orderBy('handover_certificates.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getHandoverById(int $id)
    {
        $builder = $this->builder();
        $builder->select('handover_certificates.*, bookings.booking_number, bookings.final_amount, bookings.booking_date, customers.first_name, customers.last_name, customers.email, customers.phone, customers.address, customers.id_proof_type, customers.id_proof_number, property_units.unit_number, property_units.floor, property_units.carpet_area, property_units.built_up_area, projects.name as project_name, projects.project_code, project_towers.tower_name, users.name as authorizer_name');
        $builder->join('bookings', 'bookings.id = handover_certificates.booking_id', 'left');
        $builder->join('customers', 'customers.id = handover_certificates.customer_id', 'left');
        $builder->join('property_units', 'property_units.id = handover_certificates.property_unit_id', 'left');
        $builder->join('projects', 'projects.id = property_units.project_id', 'left');
        $builder->join('project_towers', 'project_towers.id = property_units.tower_id', 'left');
        $builder->join('users', 'users.id = handover_certificates.authorized_by', 'left');
        $builder->where('handover_certificates.id', $id);

        return $builder->get()->getRowArray();
    }
}
