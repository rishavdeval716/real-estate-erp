<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\PropertyStatusHistoryModel;
use App\Models\ProjectModel;
use App\Models\AuditLogModel;
use App\Libraries\PropertyStatus;

class PropertyAvailabilityController extends BaseController
{
    protected $propertyModel;
    protected $unitModel;
    protected $statusHistoryModel;
    protected $projectModel;

    public function __construct()
    {
        $this->propertyModel      = new PropertyModel();
        $this->unitModel          = new PropertyUnitModel();
        $this->statusHistoryModel = new PropertyStatusHistoryModel();
        $this->projectModel       = new ProjectModel();
    }

    /**
     * View system-wide availability status transition history
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $perPage = 15;

        $history = $this->statusHistoryModel
                        ->select('
                            property_status_history.*,
                            properties.title as property_title,
                            properties.property_code,
                            property_units.unit_number,
                            users.name as user_name
                        ')
                        ->join('properties', 'properties.id = property_status_history.property_id', 'left')
                        ->join('property_units', 'property_units.id = property_status_history.unit_id', 'left')
                        ->join('users', 'users.id = property_status_history.changed_by', 'left')
                        ->orderBy('property_status_history.id', 'DESC')
                        ->paginate($perPage);

        $pager = $this->statusHistoryModel->pager;

        $data = [
            'title'   => 'Availability History - Real Estate ERP',
            'history' => $history,
            'pager'   => $pager,
        ];

        return view('availability/index', $data);
    }

    /**
     * Update Property Availability Status
     */
    public function updatePropertyStatus()
    {
        $propertyId = (int)$this->request->getPost('property_id');
        $newStatus  = trim($this->request->getPost('status') ?? '');
        $remarks    = trim($this->request->getPost('remarks') ?? '');

        if (!PropertyStatus::isValid($newStatus)) {
            return redirect()->back()->with('error', 'Invalid status selected.');
        }

        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return redirect()->back()->with('error', 'Property not found.');
        }

        $oldStatus = $property['status'];
        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', "Property status is already '{$newStatus}'.");
        }

        // 1. Update current status
        $this->propertyModel->update($propertyId, ['status' => $newStatus]);

        // 2. Insert status history
        $userId = session()->get('user_id');
        $this->statusHistoryModel->recordChange(
            $propertyId,
            null,
            $oldStatus,
            $newStatus,
            $userId,
            $remarks ?: "Status transitioned from {$oldStatus} to {$newStatus}"
        );

        // 3. Create audit log
        AuditLogModel::record(
            'PROPERTY_STATUS_CHANGED',
            'Availability',
            $propertyId,
            "Changed property '{$property['title']}' ({$property['property_code']}) status from '{$oldStatus}' to '{$newStatus}'"
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', "Property availability status successfully changed to '{$newStatus}'.");
    }

    /**
     * Update Property Unit Availability Status
     */
    public function updateUnitStatus()
    {
        $unitId    = (int)$this->request->getPost('unit_id');
        $newStatus = trim($this->request->getPost('status') ?? '');
        $remarks   = trim($this->request->getPost('remarks') ?? '');

        if (!PropertyStatus::isValid($newStatus)) {
            return redirect()->back()->with('error', 'Invalid status selected.');
        }

        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return redirect()->back()->with('error', 'Unit not found.');
        }

        $oldStatus = $unit['availability_status'];
        if ($oldStatus === $newStatus) {
            return redirect()->back()->with('info', "Unit status is already '{$newStatus}'.");
        }

        // 1. Update current status
        $this->unitModel->update($unitId, ['availability_status' => $newStatus]);

        // 2. Sync project counts dynamically
        $this->projectModel->syncUnitCounts($unit['project_id']);

        // 3. Insert status history if property is linked
        $userId = session()->get('user_id');
        if ($unit['property_id']) {
            $this->statusHistoryModel->recordChange(
                $unit['property_id'],
                $unitId,
                $oldStatus,
                $newStatus,
                $userId,
                $remarks ?: "Unit {$unit['unit_number']} status changed from {$oldStatus} to {$newStatus}"
            );
        }

        // 4. Create audit log
        AuditLogModel::record(
            'UNIT_STATUS_CHANGED',
            'Availability',
            $unitId,
            "Changed unit {$unit['unit_number']} status from '{$oldStatus}' to '{$newStatus}'"
        );

        return redirect()->to("/units/view/{$unitId}")->with('success', "Unit availability status updated to '{$newStatus}'.");
    }
}
