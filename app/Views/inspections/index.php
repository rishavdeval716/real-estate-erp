<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Structural, MEP, safety checks, and pre-possession snagging checklists</p>
    </div>
    <div>
        <a href="/inspections/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Schedule Inspection
        </a>
    </div>
</div>

<!-- Filters -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/inspections" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Project</label>
            <select name="project_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Projects</option>
                <?php foreach ($projects as $prj): ?>
                    <option value="<?= $prj['id'] ?>" <?= $selectedProject == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Inspection Result</label>
            <select name="result" class="form-control" onchange="this.form.submit()">
                <option value="">All Results</option>
                <option value="Passed" <?= $selectedResult === 'Passed' ? 'selected' : '' ?>>Passed</option>
                <option value="Conditional Pass" <?= $selectedResult === 'Conditional Pass' ? 'selected' : '' ?>>Conditional Pass</option>
                <option value="Failed" <?= $selectedResult === 'Failed' ? 'selected' : '' ?>>Failed</option>
            </select>
        </div>
        <?php if ($selectedProject || $selectedResult): ?>
            <div>
                <a href="/inspections" class="btn btn-outline" style="padding: 10px 16px;">Reset</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Inspections Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Inspection Date</th>
                        <th>Inspection Type</th>
                        <th>Location (Project / Unit)</th>
                        <th>Quality Result</th>
                        <th>Snags Logged</th>
                        <th>Inspector</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inspections)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-500); padding: 32px;">No site inspections recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inspections as $insp): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($insp['inspection_code']) ?></td>
                                <td><?= date('d M Y', strtotime($insp['inspection_date'])) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($insp['inspection_type']) ?></div>
                                    <?php if ($insp['remarks']): ?>
                                        <div style="font-size: 11px; color: var(--slate-500);"><?= esc($insp['remarks']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= esc($insp['project_name']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($insp['tower_name'] ?? 'Main') ?><?= $insp['unit_number'] ? ' - Unit ' . esc($insp['unit_number']) : '' ?></div>
                                </td>
                                <td>
                                    <?php if ($insp['result'] === 'Passed'): ?>
                                        <span class="badge badge-success">Passed</span>
                                    <?php elseif ($insp['result'] === 'Conditional Pass'): ?>
                                        <span class="badge badge-warning">Conditional Pass</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($insp['snags_found'] > 0): ?>
                                        <div style="color: var(--danger); font-weight: 700;"><?= $insp['snags_found'] ?> Snags</div>
                                        <div style="font-size: 11px; color: var(--slate-500); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($insp['snag_details']) ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--success); font-weight: 600;">0 Snags</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 12px; color: var(--slate-600);"><?= esc($insp['inspector_name'] ?? 'Quality Engineer') ?></td>
                                <td>
                                    <?php if ($insp['status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php elseif ($insp['status'] === 'Action Required'): ?>
                                        <span class="badge badge-danger">Action Required</span>
                                    <?php elseif ($insp['status'] === 'Rectified & Closed'): ?>
                                        <span class="badge badge-info">Rectified & Closed</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($insp['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($insp['status'] === 'Action Required'): ?>
                                        <form method="POST" action="/inspections/rectify/<?= $insp['id'] ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success">Close Snags</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--slate-400);">Signed off</span>
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
