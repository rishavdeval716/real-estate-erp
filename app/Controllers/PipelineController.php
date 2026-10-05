<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\AuditLogModel;
use App\Libraries\LeadStatus;

class PipelineController extends BaseController
{
    protected $leadModel;
    protected $auditModel;

    public function __construct()
    {
        $this->leadModel  = new LeadModel();
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Render the Kanban visual pipeline board
     */
    public function index()
    {
        $pipeline = $this->leadModel->getPipelineLeadsByStage();
        $stages   = LeadStatus::getStages();

        // Calculate pipeline value & totals
        $totalPipelineCount = 0;
        $totalPipelineValue = 0.0;
        $activeOpportunities = 0;

        foreach ($pipeline as $st => $leads) {
            $totalPipelineCount += count($leads);
            if (!in_array($st, ['Won', 'Lost'], true)) {
                $activeOpportunities += count($leads);
            }
            foreach ($leads as $l) {
                if ($l['budget_max'] > 0) {
                    $totalPipelineValue += (float)$l['budget_max'];
                } elseif ($l['budget_min'] > 0) {
                    $totalPipelineValue += (float)$l['budget_min'];
                }
            }
        }

        $data = [
            'title'               => 'CRM Sales Pipeline - Real Estate ERP',
            'pipeline'            => $pipeline,
            'stages'              => $stages,
            'totalPipelineCount'  => $totalPipelineCount,
            'totalPipelineValue'  => $totalPipelineValue,
            'activeOpportunities' => $activeOpportunities,
        ];

        return view('pipeline/index', $data);
    }

    /**
     * Quick stage transition endpoint
     */
    public function updateStage()
    {
        $leadId   = (int)$this->request->getPost('lead_id');
        $newStage = trim($this->request->getPost('lead_stage') ?? '');
        $remarks  = trim($this->request->getPost('remarks') ?? '');

        $lead = $this->leadModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        if (!in_array($newStage, LeadStatus::getStages(), true)) {
            return redirect()->back()->with('error', 'Invalid target stage.');
        }

        $update = ['lead_stage' => $newStage];
        if ($newStage === 'Won') {
            $update['lead_status'] = 'Converted';
        } elseif ($newStage === 'Lost') {
            $update['lead_status'] = 'Lost';
        } elseif (in_array($newStage, ['Qualified', 'Site Visit Scheduled', 'Site Visit Completed', 'Negotiation', 'Token Pending', 'Ready for Booking'])) {
            $update['lead_status'] = 'Qualified';
        }

        $this->leadModel->update($leadId, $update);

        $this->auditModel->record(
            session()->get('user_id'),
            'PIPELINE_STAGE_MOVED',
            'Pipeline',
            $leadId,
            "Moved {$lead['lead_code']} from {$lead['lead_stage']} to {$newStage}" . ($remarks ? " [Remarks: {$remarks}]" : '')
        );

        return redirect()->to('/pipeline')->with('success', "Lead {$lead['lead_code']} moved to '{$newStage}' successfully.");
    }
}
