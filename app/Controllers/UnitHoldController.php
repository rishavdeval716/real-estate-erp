<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UnitHoldModel;
use App\Models\LeadModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\AuditLogModel;

class UnitHoldController extends BaseController
{
    protected $holdModel;
    protected $unitModel;
    protected $leadModel;
    protected $statusHistModel;
    protected $auditModel;

    public function __construct()
    {
        $this->holdModel       = new UnitHoldModel();
        $this->unitModel       = new PropertyUnitModel();
        $this->leadModel       = new LeadModel();
        $this->statusHistModel = new PropertyStatusHistoryModel();
        $this->auditModel      = new AuditLogModel();
    }

    /**
     * List active and historical unit holds
     */
    public function index()
    {
        $status = trim($this->request->getGet('status') ?? 'Active');
        $this->holdModel->expireOverdueHolds(); // Refresh on-access

        $builder = $this->holdModel->select('unit_holds.*, 
            leads.lead_code, 
            leads.first_name, 
            leads.last_name, 
            leads.phone as lead_phone,
            properties.title as property_title,
            properties.property_code,
            property_units.unit_number,
            property_units.flat_type,
            property_units.unit_price,
            users.name as held_by_name')
            ->join('leads', 'leads.id = unit_holds.lead_id', 'left')
            ->join('properties', 'properties.id = unit_holds.property_id', 'left')
            ->join('property_units', 'property_units.id = unit_holds.property_unit_id', 'left')
            ->join('users', 'users.id = unit_holds.held_by', 'left');

        if (!empty($status) && $status !== 'all') {
            $builder->where('unit_holds.hold_status', $status);
        }

        $holds = $builder->orderBy('unit_holds.created_at', 'DESC')->paginate(15);

        $kpi = [
            'active'   => $this->holdModel->where('hold_status', 'Active')->countAllResults(),
            'expired'  => $this->holdModel->where('hold_status', 'Expired')->countAllResults(),
            'released' => $this->holdModel->where('hold_status', 'Released')->countAllResults(),
            'total'    => $this->holdModel->countAllResults(),
        ];

        $data = [
            'title'    => 'Temporary Unit Holds & Token Reservations - Real Estate ERP',
            'holds'    => $holds,
            'pager'    => $this->holdModel->pager,
            'status'   => $status,
            'kpi'      => $kpi,
            'statuses' => ['Active', 'Expired', 'Released', 'Converted'],
        ];

        return view('unit_holds/index', $data);
    }

    /**
     * Create a temporary unit hold
     */
    public function store()
    {
        $leadId = (int)$this->request->getPost('lead_id');
        $unitId = (int)$this->request->getPost('property_unit_id');
        $durationHours = (int)($this->request->getPost('duration_hours') ?: 48);
        $reason = trim($this->request->getPost('hold_reason') ?? '');

        $lead = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Valid lead is required to place a hold.');
        }

        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->back()->with('error', 'Property unit not found.');
        }

        // Rule 1: Only AVAILABLE units can be held!
        if ($unit['availability_status'] !== 'Available') {
            return redirect()->back()->with('error', "Cannot hold Unit {$unit['unit_number']}. Current status is '{$unit['availability_status']}'. Only 'Available' units can be held.");
        }

        // Rule 2: Prevent duplicate active hold on the same unit
        $existingHold = $this->holdModel->getActiveHoldForUnit($unitId);
        if ($existingHold) {
            return redirect()->back()->with('error', "Unit {$unit['unit_number']} already has an active hold ({$existingHold['hold_code']}).");
        }

        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$durationHours} hours"));
        $code = $this->holdModel->generateHoldCode();
        $userId = session()->get('user_id');

        // Create Hold
        $holdId = $this->holdModel->insert([
            'hold_code'        => $code,
            'lead_id'          => $leadId,
            'property_id'      => $unit['property_id'] ?: null,
            'property_unit_id' => $unitId,
            'held_by'          => $userId,
            'hold_status'      => 'Active',
            'hold_reason'      => $reason ?: 'Token negotiation hold',
            'started_at'       => $now,
            'expires_at'       => $expiresAt,
            'released_at'      => null,
            'remarks'          => trim($this->request->getPost('remarks') ?? ''),
        ]);

        // Transition Unit Status: Available -> Reserved
        $this->unitModel->update($unitId, [
            'availability_status' => 'Reserved',
        ]);

        // Status History
        $this->statusHistModel->insert([
            'property_id' => $unit['property_id'] ?: null,
            'unit_id'     => $unitId,
            'old_status'  => 'Available',
            'new_status'  => 'Reserved',
            'changed_by'  => $userId,
            'remarks'     => "Temporary reservation hold {$code} placed for Lead {$lead['lead_code']} (valid for {$durationHours}h).",
            'created_at'  => $now,
        ]);

        // Advance Lead Stage to 'Token Pending' if in earlier stages
        if (in_array($lead['lead_stage'], ['New', 'Contacted', 'Qualified', 'Site Visit Scheduled', 'Site Visit Completed', 'Negotiation'], true)) {
            $this->leadModel->update($leadId, [
                'lead_stage'       => 'Token Pending',
                'lead_status'      => 'Qualified',
                'property_unit_id' => $unitId,
            ]);
        }

        // Audit Log
        $this->auditModel->record(
            $userId,
            'UNIT_HOLD_CREATED',
            'UnitHolds',
            $holdId,
            "Placed temporary hold {$code} on Unit {$unit['unit_number']} for Lead {$lead['lead_code']} until {$expiresAt}"
        );

        $redirectUrl = $this->request->getPost('redirect_to') ?: "/leads/view/{$leadId}";
        return redirect()->to($redirectUrl)->with('success', "Unit Hold {$code} active until {$expiresAt}. Unit {$unit['unit_number']} marked as Reserved.");
    }

    /**
     * Release a unit hold early, returning the unit to Available
     */
    public function release($id)
    {
        $hold = $this->holdModel->find($id);
        if (!$hold) {
            return redirect()->back()->with('error', 'Hold not found.');
        }

        if ($hold['hold_status'] !== 'Active') {
            return redirect()->back()->with('error', "Hold {$hold['hold_code']} is already {$hold['hold_status']}.");
        }

        $now    = date('Y-m-d H:i:s');
        $userId = session()->get('user_id');
        $reason = trim($this->request->getPost('release_reason') ?? '');

        // Update hold
        $this->holdModel->update($id, [
            'hold_status' => 'Released',
            'released_at' => $now,
            'remarks'     => $hold['remarks'] . ($reason ? " \n[Released: {$reason}]" : ''),
        ]);

        // Restore unit to Available if currently Reserved
        $unit = $this->unitModel->find($hold['property_unit_id']);
        if ($unit && $unit['availability_status'] === 'Reserved') {
            $this->unitModel->update($unit['id'], [
                'availability_status' => 'Available',
            ]);

            // Record status history
            $this->statusHistModel->insert([
                'property_id' => $unit['property_id'] ?: null,
                'unit_id'     => $unit['id'],
                'old_status'  => 'Reserved',
                'new_status'  => 'Available',
                'changed_by'  => $userId,
                'remarks'     => "Hold {$hold['hold_code']} released manually by manager. Unit returned to Available.",
                'created_at'  => $now,
            ]);
        }

        // Audit log
        $this->auditModel->record(
            $userId,
            'UNIT_HOLD_RELEASED',
            'UnitHolds',
            $id,
            "Released unit hold {$hold['hold_code']} on Unit {$unit['unit_number']}. Restored to Available."
        );

        return redirect()->back()->with('success', "Hold {$hold['hold_code']} released. Unit {$unit['unit_number']} is now Available.");
    }
}
