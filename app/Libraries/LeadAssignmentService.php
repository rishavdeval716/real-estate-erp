<?php

namespace App\Libraries;

use App\Models\LeadModel;
use App\Models\LeadAssignmentModel;
use App\Models\AuditLogModel;
use App\Models\UserModel;

class LeadAssignmentService
{
    /**
     * Get active users eligible for lead assignment
     */
    public static function getEligibleExecutives(): array
    {
        $db = \Config\Database::connect();
        
        // Find users with active status and either Sales Executive role, Manager role, or leads permissions
        $rows = $db->table('users')
            ->select('users.id, users.name, users.email')
            ->where('users.status', 'active')
            ->where('users.deleted_at', null)
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();

        return $rows;
    }

    /**
     * Assign or reassign lead to a specific executive
     */
    public static function assignLead(int $leadId, int $assignedToId, ?int $assignedById = null, string $remarks = '', bool $isReassignment = false): bool
    {
        $leadModel = new LeadModel();
        $lead = $leadModel->find($leadId);
        if (!$lead) {
            return false;
        }

        $userModel = new UserModel();
        $targetUser = $userModel->find($assignedToId);
        if (!$targetUser || $targetUser['status'] !== 'active') {
            return false;
        }

        $assignmentType = $isReassignment ? 'Reassignment' : 'Initial';

        // Update lead
        $leadModel->update($leadId, [
            'assigned_user_id' => $assignedToId,
        ]);

        // Insert into lead_assignments
        $assignModel = new LeadAssignmentModel();
        $assignModel->insert([
            'lead_id'         => $leadId,
            'assigned_to'     => $assignedToId,
            'assigned_by'     => $assignedById,
            'assignment_type' => $assignmentType,
            'remarks'         => $remarks ?: ($isReassignment ? 'Lead reassigned' : 'Initial assignment'),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // Audit log
        $auditModel = new AuditLogModel();
        $auditModel->record(
            $assignedById ?: $assignedToId,
            $isReassignment ? 'LEAD_REASSIGNED' : 'LEAD_ASSIGNED',
            'Leads',
            $leadId,
            "Lead {$lead['lead_code']} assigned to {$targetUser['name']} ({$assignmentType})"
        );

        return true;
    }

    /**
     * Round-robin assignment to the executive with the least active assigned leads
     */
    public static function assignRoundRobin(int $leadId, ?int $assignedById = null, string $remarks = ''): ?int
    {
        $executives = self::getEligibleExecutives();
        if (empty($executives)) {
            return null;
        }

        $db = \Config\Database::connect();
        $execIds = array_column($executives, 'id');

        // Count current active leads per eligible executive
        $counts = $db->table('leads')
            ->select('assigned_user_id, COUNT(*) as lead_count')
            ->whereIn('assigned_user_id', $execIds)
            ->whereNotIn('lead_status', ['Converted', 'Lost'])
            ->where('deleted_at', null)
            ->groupBy('assigned_user_id')
            ->get()
            ->getResultArray();

        $countMap = [];
        foreach ($execIds as $id) {
            $countMap[$id] = 0;
        }
        foreach ($counts as $c) {
            if (isset($countMap[$c['assigned_user_id']])) {
                $countMap[$c['assigned_user_id']] = (int)$c['lead_count'];
            }
        }

        // Sort by lead count ASC
        asort($countMap);
        $selectedUserId = array_key_first($countMap);

        if ($selectedUserId) {
            self::assignLead($leadId, $selectedUserId, $assignedById, $remarks ?: 'Assigned via Round-Robin distribution', false);
            return $selectedUserId;
        }

        return null;
    }
}
