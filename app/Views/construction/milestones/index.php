<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Physical stage milestones, completion weightages, and structural sign-offs</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('addMilestoneModal').style.display='flex';" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Stage Milestone
        </button>
    </div>
</div>

<!-- Project Filter & Overall Progress Bar -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/construction/milestones" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 20px;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Select Project</label>
            <select name="project_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Projects</option>
                <?php foreach ($projects as $prj): ?>
                    <option value="<?= $prj['id'] ?>" <?= $selectedProject == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name'] ?? $prj['project_name']) ?> (<?= esc($prj['project_code']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Select Tower</label>
            <select name="tower_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Towers</option>
                <?php foreach ($towers as $tw): ?>
                    <option value="<?= $tw['id'] ?>" <?= $selectedTower == $tw['id'] ? 'selected' : '' ?>><?= esc($tw['tower_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($selectedProject || $selectedTower): ?>
            <div>
                <a href="/construction/milestones" class="btn btn-outline" style="padding: 10px 16px;">Reset Filter</a>
            </div>
        <?php endif; ?>
    </form>

    <div>
        <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 8px;">
            <span>Calculated Physical Progress (Weightage Adjusted)</span>
            <span style="color: var(--primary); font-size: 16px; font-weight: 700;"><?= $overallProgress ?>%</span>
        </div>
        <div style="width: 100%; height: 12px; background: var(--slate-100); border-radius: 99px; overflow: hidden;">
            <div style="width: <?= $overallProgress ?>%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--success)); border-radius: 99px; transition: width 0.4s ease;"></div>
        </div>
    </div>
</div>

<!-- Milestones Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Milestone Code</th>
                        <th>Stage / Milestone Name</th>
                        <th>Tower</th>
                        <th>Weightage</th>
                        <th>Target Dates</th>
                        <th>Progress %</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($milestones)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--slate-500); padding: 32px;">No construction milestones found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($milestones as $ms): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--slate-600);"><?= esc($ms['stage_order']) ?></td>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($ms['milestone_code']) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($ms['milestone_name']) ?></div>
                                    <div style="font-size: 12px; color: var(--slate-500);"><?= esc($ms['project_name']) ?></div>
                                </td>
                                <td><?= esc($ms['tower_name'] ?? 'All Towers') ?></td>
                                <td><strong><?= esc($ms['weightage_percentage']) ?>%</strong></td>
                                <td style="font-size: 12px;">
                                    <div>Target: <?= $ms['target_completion_date'] ? date('d M Y', strtotime($ms['target_completion_date'])) : '-' ?></div>
                                    <?php if ($ms['actual_completion_date']): ?>
                                        <div style="color: var(--success); font-weight: 600;">Actual: <?= date('d M Y', strtotime($ms['actual_completion_date'])) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="min-width: 140px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 4px;">
                                        <span><?= esc($ms['progress_percentage']) ?>%</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: var(--slate-100); border-radius: 99px; overflow: hidden;">
                                        <div style="width: <?= (float)$ms['progress_percentage'] ?>%; height: 100%; background: <?= $ms['status'] === 'Completed' ? 'var(--success)' : 'var(--primary)' ?>; border-radius: 99px;"></div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($ms['status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php elseif ($ms['status'] === 'In Progress'): ?>
                                        <span class="badge badge-info">In Progress</span>
                                    <?php elseif ($ms['status'] === 'Delayed'): ?>
                                        <span class="badge badge-danger">Delayed</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($ms['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 12px;">
                                    <?php if ($ms['verified_by']): ?>
                                        <div style="color: var(--success); font-weight: 600;">✓ Verified</div>
                                        <div style="color: var(--slate-500);"><?= esc($ms['verifier_name'] ?? 'Engineer') ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400);">Pending Sign-off</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openUpdateModal(<?= htmlspecialchars(json_encode($ms), ENT_QUOTES, 'UTF-8') ?>)">
                                        Update Progress
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Milestone -->
<div id="addMilestoneModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div class="erp-card" style="width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; background: #fff; border-radius: 12px; box-shadow: var(--shadow-xl);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="font-size: 17px;">Add Construction Milestone</h2>
            <button type="button" onclick="document.getElementById('addMilestoneModal').style.display='none';" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/construction/milestones/store" style="padding: 20px;">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Project *</label>
                    <select name="project_id" class="form-control" required>
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $prj): ?>
                            <option value="<?= $prj['id'] ?>"><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Building Tower (Optional)</label>
                    <select name="tower_id" class="form-control">
                        <option value="">All Towers / Site-wide</option>
                        <?php foreach ($towers as $tw): ?>
                            <option value="<?= $tw['id'] ?>"><?= esc($tw['tower_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Milestone Stage Name *</label>
                    <input type="text" name="milestone_name" class="form-control" placeholder="e.g. 5th Floor Slab Casting" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Stage Order *</label>
                        <input type="number" name="stage_order" class="form-control" value="1" min="1" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Weightage % *</label>
                        <input type="number" name="weightage_percentage" class="form-control" value="10" step="0.1" min="0" max="100" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Target Start Date</label>
                        <input type="date" name="target_start_date" class="form-control">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Target Completion Date</label>
                        <input type="date" name="target_completion_date" class="form-control">
                    </div>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="Engineering specifications or notes..."></textarea>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addMilestoneModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Milestone</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Update Milestone Progress -->
<div id="updateMilestoneModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div class="erp-card" style="width: 100%; max-width: 500px; background: #fff; border-radius: 12px; box-shadow: var(--shadow-xl);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" id="updateModalTitle" style="font-size: 17px;">Update Milestone Progress</h2>
            <button type="button" onclick="document.getElementById('updateMilestoneModal').style.display='none';" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="updateMilestoneForm" method="POST" action="" style="padding: 20px;">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Physical Progress (%) *</label>
                    <input type="number" id="updateProgressInput" name="progress_percentage" class="form-control" min="0" max="100" step="0.5" required>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Stage Status *</label>
                    <select id="updateStatusSelect" name="status" class="form-control" required>
                        <option value="Not Started">Not Started</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Under Review">Under Review</option>
                        <option value="Completed">Completed</option>
                        <option value="Delayed">Delayed</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Site Engineer Verification Remarks</label>
                    <textarea id="updateRemarksInput" name="remarks" class="form-control" rows="3" placeholder="Notes on site inspection, cube tests, or completed sections..."></textarea>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('updateMilestoneModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Update & Sign-off</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateModal(ms) {
    document.getElementById('updateModalTitle').innerText = 'Update: ' + ms.milestone_name;
    document.getElementById('updateMilestoneForm').action = '/construction/milestones/update/' + ms.id;
    document.getElementById('updateProgressInput').value = ms.progress_percentage;
    document.getElementById('updateStatusSelect').value = ms.status;
    document.getElementById('updateRemarksInput').value = ms.remarks || '';
    document.getElementById('updateMilestoneModal').style.display = 'flex';
}
</script>
<?= $this->endSection() ?>
