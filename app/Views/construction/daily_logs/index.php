<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Supervisor daily site logs, manpower counts, weather conditions, and work execution records</p>
    </div>
    <div>
        <a href="/construction/daily-logs/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Record Daily Site Log
        </a>
    </div>
</div>

<!-- Filters -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/construction/daily-logs" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Project</label>
            <select name="project_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Projects</option>
                <?php foreach ($projects as $prj): ?>
                    <option value="<?= $prj['id'] ?>" <?= $selectedProject == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Date</label>
            <input type="date" name="date" class="form-control" value="<?= esc($selectedDate) ?>" onchange="this.form.submit()">
        </div>
        <?php if ($selectedProject || $selectedDate): ?>
            <div>
                <a href="/construction/daily-logs" class="btn btn-outline" style="padding: 10px 16px;">Reset Filter</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Logs Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Log Code</th>
                        <th>Execution Date</th>
                        <th>Project & Tower</th>
                        <th>Active Milestone</th>
                        <th>Weather</th>
                        <th>Manpower</th>
                        <th>Work Executed Description</th>
                        <th>Logged By</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--slate-500); padding: 32px;">No daily site progress logs found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($log['log_code']) ?></td>
                                <td><strong><?= date('d M Y', strtotime($log['log_date'])) ?></strong></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($log['project_name']) ?></div>
                                    <div style="font-size: 12px; color: var(--slate-500);"><?= esc($log['tower_name'] ?? 'Main Site') ?></div>
                                </td>
                                <td style="font-size: 13px; color: var(--slate-700);"><?= esc($log['milestone_name'] ?? 'General Execution') ?></td>
                                <td><span class="badge badge-secondary"><?= esc($log['weather_condition']) ?></span></td>
                                <td>
                                    <div style="font-weight: 700; color: var(--slate-900);"><?= (int)$log['skilled_workers'] + (int)$log['unskilled_workers'] ?> Total</div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= $log['skilled_workers'] ?> Skilled / <?= $log['unskilled_workers'] ?> Helper</div>
                                </td>
                                <td style="max-width: 280px; font-size: 13px;">
                                    <div><?= esc($log['work_completed']) ?></div>
                                    <?php if ($log['materials_used']): ?>
                                        <div style="font-size: 11px; color: var(--slate-500); margin-top: 4px;"><strong>Materials:</strong> <?= esc($log['materials_used']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 12px; color: var(--slate-600);"><?= esc($log['logger_name'] ?? 'Supervisor') ?></td>
                                <td>
                                    <?php if ($log['status'] === 'Approved'): ?>
                                        <span class="badge badge-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Submitted</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($log['status'] === 'Submitted'): ?>
                                        <form method="POST" action="/construction/daily-logs/approve/<?= $log['id'] ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success">Approve Log</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--slate-400);">By <?= esc($log['approver_name'] ?? 'Engineer') ?></span>
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
