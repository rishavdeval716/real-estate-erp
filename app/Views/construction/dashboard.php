<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Live site execution tracking, contractor work contracts, material procurement and unit possession handover</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/construction/daily-logs/create" class="btn btn-outline-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Log Daily Work
        </a>
        <a href="/handover/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            Execute Handover
        </a>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="erp-card" style="padding: 20px; border-left: 4px solid var(--primary);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: var(--slate-500); text-transform: uppercase;">Active Projects</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--slate-900); margin-top: 4px;"><?= $underConstructionProjects ?> <span style="font-size: 14px; font-weight: 400; color: var(--slate-400);">/ <?= $totalProjects ?></span></div>
                <div style="font-size: 12px; color: var(--slate-500); margin-top: 4px;">Across <?= $totalTowers ?> Building Towers</div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="9" y1="22" x2="9" y2="18"></line><line x1="15" y1="22" x2="15" y2="18"></line></svg>
            </div>
        </div>
    </div>

    <div class="erp-card" style="padding: 20px; border-left: 4px solid var(--info);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: var(--slate-500); text-transform: uppercase;">Milestone Progress</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--slate-900); margin-top: 4px;"><?= $avgProgress ?>%</div>
                <div style="font-size: 12px; color: var(--slate-500); margin-top: 4px;"><?= $completedMilestones ?> Completed | <?= $inProgressMilestones ?> In Progress</div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </div>
        </div>
    </div>

    <div class="erp-card" style="padding: 20px; border-left: 4px solid var(--warning);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: var(--slate-500); text-transform: uppercase;">Active Work Contracts</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--slate-900); margin-top: 4px;"><?= $activeWorkOrders ?></div>
                <div style="font-size: 12px; color: var(--slate-500); margin-top: 4px;">Valued at ₹<?= number_format($totalContractValue, 0) ?></div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            </div>
        </div>
    </div>

    <div class="erp-card" style="padding: 20px; border-left: 4px solid var(--success);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div style="font-size: 13px; font-weight: 600; color: var(--slate-500); text-transform: uppercase;">Possession Handovers</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--slate-900); margin-top: 4px;"><?= $totalHandovers ?></div>
                <div style="font-size: 12px; color: var(--slate-500); margin-top: 4px;">Keys Issued & Meters Handed Over</div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
    <!-- Active Construction Milestones -->
    <div class="erp-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="font-size: 16px;">Physical Construction Stages & Progress</h2>
            <a href="/construction/milestones" style="font-size: 13px; font-weight: 600;">View All &rarr;</a>
        </div>
        <div class="card-body" style="padding: 16px 20px;">
            <?php if (empty($activeMilestones)): ?>
                <p style="color: var(--slate-500); text-align: center; padding: 20px;">No construction milestones defined yet.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach (array_slice($activeMilestones, 0, 5) as $ms): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 6px;">
                                <span><?= esc($ms['stage_order']) ?>. <?= esc($ms['milestone_name']) ?> <span style="color: var(--slate-400); font-weight: 400; font-size: 12px;">(<?= esc($ms['tower_name'] ?? 'All Towers') ?>)</span></span>
                                <span>
                                    <?php if ($ms['status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed (100%)</span>
                                    <?php elseif ($ms['status'] === 'In Progress'): ?>
                                        <span class="badge badge-info"><?= esc($ms['progress_percentage']) ?>% In Progress</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($ms['status']) ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div style="width: 100%; height: 8px; background: var(--slate-100); border-radius: 99px; overflow: hidden;">
                                <div style="width: <?= (float)$ms['progress_percentage'] ?>%; height: 100%; background: <?= $ms['status'] === 'Completed' ? 'var(--success)' : 'var(--primary)' ?>; border-radius: 99px; transition: width 0.4s ease;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Operational Actions & Site Logs -->
    <div class="erp-card">
        <div class="card-header">
            <h2 class="card-title" style="font-size: 16px;">Quick Actions</h2>
        </div>
        <div class="card-body" style="padding: 16px 20px; display: flex; flex-direction: column; gap: 10px;">
            <a href="/construction/daily-logs" class="btn btn-outline-primary" style="text-align: left; justify-content: flex-start; padding: 12px 16px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                Daily Site Progress Register
            </a>
            <a href="/construction/work-orders" class="btn btn-outline-primary" style="text-align: left; justify-content: flex-start; padding: 12px 16px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><rect x="2" y="7" width="20" height="14" rx="2"></rect></svg>
                Contractor Work Contracts
            </a>
            <a href="/procurement" class="btn btn-outline-primary" style="text-align: left; justify-content: flex-start; padding: 12px 16px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path></svg>
                Material Requisitions (Indents)
            </a>
            <a href="/inspections" class="btn btn-outline-primary" style="text-align: left; justify-content: flex-start; padding: 12px 16px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M9 11l3 3L22 4"></path></svg>
                Site Quality & Safety Inspections
            </a>
            <a href="/handover" class="btn btn-outline-primary" style="text-align: left; justify-content: flex-start; padding: 12px 16px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                Possession & Key Handover Register
            </a>
        </div>
    </div>
</div>

<!-- Recent Daily Site Logs Table -->
<div class="erp-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="card-title" style="font-size: 16px;">Recent Daily Execution Logs</h2>
        <a href="/construction/daily-logs" style="font-size: 13px; font-weight: 600;">View All Logs &rarr;</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Log Code</th>
                        <th>Date</th>
                        <th>Project & Tower</th>
                        <th>Weather</th>
                        <th>Manpower</th>
                        <th>Work Executed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLogs)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-500); padding: 24px;">No daily site logs recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($log['log_code']) ?></td>
                                <td><?= date('d M Y', strtotime($log['log_date'])) ?></td>
                                <td><?= esc($log['project_name']) ?> <span style="color: var(--slate-400);">(<?= esc($log['tower_name'] ?? 'Main Site') ?>)</span></td>
                                <td><span class="badge badge-secondary"><?= esc($log['weather_condition']) ?></span></td>
                                <td><strong><?= (int)$log['skilled_workers'] + (int)$log['unskilled_workers'] ?></strong> <span style="font-size: 12px; color: var(--slate-500);">(<?= $log['skilled_workers'] ?> Sk / <?= $log['unskilled_workers'] ?> Un)</span></td>
                                <td style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($log['work_completed']) ?></td>
                                <td>
                                    <?php if ($log['status'] === 'Approved'): ?>
                                        <span class="badge badge-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Submitted</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
