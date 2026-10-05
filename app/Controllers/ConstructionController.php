<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConstructionMilestoneModel;
use App\Models\DailySiteLogModel;
use App\Models\ProjectModel;
use App\Models\ProjectTowerModel;
use App\Models\ContractorModel;
use App\Models\ConstructionWorkOrderModel;
use App\Models\MaterialRequisitionModel;
use App\Models\SiteInspectionModel;
use App\Models\HandoverCertificateModel;
use App\Models\AuditLogModel;
use App\Models\UserModel;

class ConstructionController extends BaseController
{
    protected $milestoneModel;
    protected $logModel;
    protected $projectModel;
    protected $towerModel;
    protected $contractorModel;
    protected $workOrderModel;
    protected $reqModel;
    protected $inspectionModel;
    protected $handoverModel;
    protected $userModel;

    public function __construct()
    {
        $this->milestoneModel = new ConstructionMilestoneModel();
        $this->logModel       = new DailySiteLogModel();
        $this->projectModel   = new ProjectModel();
        $this->towerModel     = new ProjectTowerModel();
        $this->contractorModel= new ContractorModel();
        $this->workOrderModel = new ConstructionWorkOrderModel();
        $this->reqModel       = new MaterialRequisitionModel();
        $this->inspectionModel= new SiteInspectionModel();
        $this->handoverModel  = new HandoverCertificateModel();
        $this->userModel      = new UserModel();
    }

    // =====================================================
    // 1. CONSTRUCTION DASHBOARD
    // =====================================================
    public function dashboard()
    {
        $db = \Config\Database::connect();

        // 1. Projects under construction
        $totalProjects = $this->projectModel->countAllResults();
        $underConstructionProjects = $this->projectModel->where('construction_status', 'Under Construction')->countAllResults();

        // 2. Towers
        $totalTowers = $this->towerModel->countAllResults();

        // 3. Milestones Stats
        $totalMilestones = $this->milestoneModel->countAllResults();
        $completedMilestones = $this->milestoneModel->where('status', 'Completed')->countAllResults();
        $inProgressMilestones = $this->milestoneModel->where('status', 'In Progress')->countAllResults();
        
        $avgProgress = 0;
        if ($totalMilestones > 0) {
            $avgQuery = $db->query('SELECT AVG(progress_percentage) as avg_p FROM construction_milestones')->getRow();
            $avgProgress = round($avgQuery->avg_p ?? 0, 1);
        }

        // 4. Manpower deployed today
        $today = date('Y-m-d');
        $manpowerQuery = $db->query("SELECT SUM(skilled_workers) as skilled, SUM(unskilled_workers) as unskilled FROM daily_site_logs WHERE log_date = ?", [$today])->getRow();
        $todayWorkers = (int)($manpowerQuery->skilled ?? 0) + (int)($manpowerQuery->unskilled ?? 0);

        // 5. Active Work Contracts
        $activeWorkOrders = $this->workOrderModel->whereIn('status', ['Awarded', 'In Progress'])->countAllResults();
        $totalContractValue = $db->query("SELECT SUM(contract_amount) as total_val FROM construction_work_orders WHERE status != 'Terminated'")->getRow()->total_val ?? 0;

        // 6. Open Requisitions
        $openRequisitions = $this->reqModel->where('status', 'Requested')->countAllResults();

        // 7. Inspections & Snags
        $totalInspections = $this->inspectionModel->countAllResults();
        $passedInspections = $this->inspectionModel->where('result', 'Passed')->countAllResults();
        $pendingSnags = $this->inspectionModel->where('status', 'Action Required')->countAllResults();

        // 8. Handovers
        $totalHandovers = $this->handoverModel->countAllResults();

        // Recent Daily Logs
        $recentLogs = $this->logModel->getLogsWithDetails(null, null);
        $recentLogs = array_slice($recentLogs, 0, 5);

        // Active Milestones
        $activeMilestones = $this->milestoneModel->getMilestonesWithDetails();

        $data = [
            'title'                      => 'Construction & Project Execution Dashboard',
            'totalProjects'              => $totalProjects,
            'underConstructionProjects'  => $underConstructionProjects,
            'totalTowers'                => $totalTowers,
            'totalMilestones'            => $totalMilestones,
            'completedMilestones'        => $completedMilestones,
            'inProgressMilestones'       => $inProgressMilestones,
            'avgProgress'                => $avgProgress,
            'todayWorkers'               => $todayWorkers,
            'activeWorkOrders'           => $activeWorkOrders,
            'totalContractValue'         => $totalContractValue,
            'openRequisitions'           => $openRequisitions,
            'totalInspections'           => $totalInspections,
            'passedInspections'          => $passedInspections,
            'pendingSnags'               => $pendingSnags,
            'totalHandovers'             => $totalHandovers,
            'recentLogs'                 => $recentLogs,
            'activeMilestones'           => $activeMilestones,
        ];

        return view('construction/dashboard', $data);
    }

    // =====================================================
    // 2. CONSTRUCTION MILESTONES
    // =====================================================
    public function milestones()
    {
        $projectId = (int)$this->request->getGet('project_id');
        $towerId   = (int)$this->request->getGet('tower_id');

        $milestones = $this->milestoneModel->getMilestonesWithDetails($projectId ?: null, $towerId ?: null);
        $projects   = $this->projectModel->findAll();
        $towers     = $this->towerModel->findAll();

        // Calculate project overall completion
        $totalWeight = 0;
        $achievedWeight = 0;
        foreach ($milestones as $m) {
            $w = (float)$m['weightage_percentage'];
            $p = (float)$m['progress_percentage'];
            $totalWeight += $w;
            $achievedWeight += ($w * $p / 100);
        }
        $overallProgress = $totalWeight > 0 ? round(($achievedWeight / $totalWeight) * 100, 1) : 0;

        $data = [
            'title'           => 'Construction Milestones & Progress Tracking',
            'milestones'      => $milestones,
            'projects'        => $projects,
            'towers'          => $towers,
            'selectedProject' => $projectId,
            'selectedTower'   => $towerId,
            'overallProgress' => $overallProgress,
        ];

        return view('construction/milestones/index', $data);
    }

    public function storeMilestone()
    {
        $rules = [
            'project_id'             => 'required|numeric',
            'milestone_name'         => 'required|min_length[3]|max_length[255]',
            'weightage_percentage'   => 'required|numeric',
            'target_start_date'      => 'permit_empty|valid_date',
            'target_completion_date' => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $code = $this->milestoneModel->generateMilestoneCode();

        $milestoneData = [
            'milestone_code'         => $code,
            'project_id'             => (int)$this->request->getPost('project_id'),
            'tower_id'               => (int)$this->request->getPost('tower_id') ?: null,
            'milestone_name'         => trim($this->request->getPost('milestone_name')),
            'stage_order'            => (int)$this->request->getPost('stage_order') ?: 1,
            'weightage_percentage'   => (float)$this->request->getPost('weightage_percentage'),
            'target_start_date'      => $this->request->getPost('target_start_date') ?: null,
            'target_completion_date' => $this->request->getPost('target_completion_date') ?: null,
            'progress_percentage'    => (float)$this->request->getPost('progress_percentage') ?: 0.00,
            'status'                 => $this->request->getPost('status') ?: 'Not Started',
            'remarks'                => trim($this->request->getPost('remarks') ?? ''),
        ];

        $insertedId = $this->milestoneModel->insert($milestoneData);

        AuditLogModel::record(
            'CONSTRUCTION_MILESTONE_CREATED',
            'Construction',
            $insertedId,
            "Created construction milestone {$code}: {$milestoneData['milestone_name']}"
        );

        return redirect()->to('/construction/milestones')->with('success', "Milestone {$code} created successfully.");
    }

    public function updateMilestone(int $id)
    {
        $milestone = $this->milestoneModel->find($id);
        if (!$milestone) {
            return redirect()->to('/construction/milestones')->with('error', 'Milestone not found.');
        }

        $progress = (float)$this->request->getPost('progress_percentage');
        $status   = trim($this->request->getPost('status'));
        $remarks  = trim($this->request->getPost('remarks') ?? '');
        $userId   = session()->get('user_id') ?: 1;

        $updateData = [
            'progress_percentage' => max(0, min(100, $progress)),
            'status'              => $status,
            'remarks'             => $remarks,
        ];

        if ($status === 'Completed' || $progress >= 100) {
            $updateData['status']                 = 'Completed';
            $updateData['progress_percentage']    = 100.00;
            $updateData['actual_completion_date'] = date('Y-m-d');
            $updateData['verified_by']            = $userId;
            $updateData['verified_at']            = date('Y-m-d H:i:s');
        }

        $this->milestoneModel->update($id, $updateData);

        AuditLogModel::record(
            'CONSTRUCTION_MILESTONE_UPDATED',
            'Construction',
            $id,
            "Updated milestone {$milestone['milestone_code']} progress to {$updateData['progress_percentage']}% ({$updateData['status']})"
        );

        return redirect()->to('/construction/milestones')->with('success', "Milestone {$milestone['milestone_code']} updated successfully.");
    }

    // =====================================================
    // 3. DAILY SITE LOGS
    // =====================================================
    public function dailyLogs()
    {
        $projectId = (int)$this->request->getGet('project_id');
        $date      = trim($this->request->getGet('date') ?? '');

        $logs     = $this->logModel->getLogsWithDetails($projectId ?: null, $date ?: null);
        $projects = $this->projectModel->findAll();

        $data = [
            'title'           => 'Daily Site Progress Logs',
            'logs'            => $logs,
            'projects'        => $projects,
            'selectedProject' => $projectId,
            'selectedDate'    => $date,
        ];

        return view('construction/daily_logs/index', $data);
    }

    public function createDailyLog()
    {
        $projects   = $this->projectModel->findAll();
        $towers     = $this->towerModel->findAll();
        $milestones = $this->milestoneModel->findAll();

        $data = [
            'title'      => 'Submit Daily Site Progress Log',
            'projects'   => $projects,
            'towers'     => $towers,
            'milestones' => $milestones,
            'today'      => date('Y-m-d'),
        ];

        return view('construction/daily_logs/create', $data);
    }

    public function storeDailyLog()
    {
        $rules = [
            'project_id'        => 'required|numeric',
            'log_date'          => 'required|valid_date',
            'skilled_workers'   => 'required|numeric',
            'unskilled_workers' => 'required|numeric',
            'work_completed'    => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $code   = $this->logModel->generateLogCode();
        $userId = session()->get('user_id') ?: 1;

        $logData = [
            'log_code'              => $code,
            'log_date'              => $this->request->getPost('log_date'),
            'project_id'            => (int)$this->request->getPost('project_id'),
            'tower_id'              => (int)$this->request->getPost('tower_id') ?: null,
            'milestone_id'          => (int)$this->request->getPost('milestone_id') ?: null,
            'skilled_workers'       => (int)$this->request->getPost('skilled_workers'),
            'unskilled_workers'     => (int)$this->request->getPost('unskilled_workers'),
            'weather_condition'     => trim($this->request->getPost('weather_condition') ?: 'Sunny'),
            'work_completed'        => trim($this->request->getPost('work_completed')),
            'materials_used'        => trim($this->request->getPost('materials_used') ?? ''),
            'equipment_deployed'    => trim($this->request->getPost('equipment_deployed') ?? ''),
            'delays_or_impediments' => trim($this->request->getPost('delays_or_impediments') ?? ''),
            'logged_by'             => $userId,
            'status'                => 'Submitted',
        ];

        $insertedId = $this->logModel->insert($logData);

        AuditLogModel::record(
            'DAILY_SITE_LOG_CREATED',
            'Construction',
            $insertedId,
            "Submitted daily site log {$code} for Date {$logData['log_date']}"
        );

        return redirect()->to('/construction/daily-logs')->with('success', "Daily site log {$code} submitted successfully.");
    }

    public function approveDailyLog(int $id)
    {
        $log = $this->logModel->find($id);
        if (!$log) {
            return redirect()->to('/construction/daily-logs')->with('error', 'Log not found.');
        }

        $userId = session()->get('user_id') ?: 1;
        $this->logModel->update($id, [
            'status'      => 'Approved',
            'approved_by' => $userId,
        ]);

        AuditLogModel::record(
            'DAILY_SITE_LOG_APPROVED',
            'Construction',
            $id,
            "Approved daily site log {$log['log_code']}"
        );

        return redirect()->to('/construction/daily-logs')->with('success', "Daily site log {$log['log_code']} approved.");
    }
}
