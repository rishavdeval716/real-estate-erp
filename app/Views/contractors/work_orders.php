<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Award and track construction packages, work contracts, retention money and execution statuses</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-primary" onclick="document.getElementById('awardWorkOrderModal').style.display='flex';" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Award Work Order
        </button>
    </div>
</div>

<!-- Filters -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/construction/work-orders" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Contractor</label>
            <select name="contractor_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Contractors</option>
                <?php foreach ($contractors as $con): ?>
                    <option value="<?= $con['id'] ?>" <?= $selectedContractor == $con['id'] ? 'selected' : '' ?>><?= esc($con['company_name']) ?> (<?= esc($con['specialization']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Project</label>
            <select name="project_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Projects</option>
                <?php foreach ($projects as $prj): ?>
                    <option value="<?= $prj['id'] ?>" <?= $selectedProject == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($selectedContractor || $selectedProject): ?>
            <div>
                <a href="/construction/work-orders" class="btn btn-outline" style="padding: 10px 16px;">Reset</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Work Orders Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>CWO Code</th>
                        <th>Package Title & Scope</th>
                        <th>Awarded Contractor</th>
                        <th>Project & Tower</th>
                        <th>Contract Value</th>
                        <th>Retention</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($workOrders)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-500); padding: 32px;">No construction work contracts awarded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($workOrders as $wo): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($wo['work_order_code']) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($wo['title']) ?></div>
                                    <div style="font-size: 12px; color: var(--slate-500); max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($wo['scope_of_work']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($wo['company_name']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($wo['specialization']) ?></div>
                                </td>
                                <td>
                                    <div><?= esc($wo['project_name']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($wo['tower_name'] ?? 'Main Complex') ?></div>
                                </td>
                                <td style="font-weight: 700; color: var(--slate-900);">₹<?= number_format((float)$wo['contract_amount'], 2) ?></td>
                                <td><?= esc($wo['retention_percentage']) ?>%</td>
                                <td style="font-size: 12px;">
                                    <div><?= date('d M Y', strtotime($wo['start_date'])) ?></div>
                                    <div style="color: var(--slate-500);">to <?= date('d M Y', strtotime($wo['completion_date'])) ?></div>
                                </td>
                                <td>
                                    <?php if ($wo['status'] === 'Completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php elseif ($wo['status'] === 'In Progress'): ?>
                                        <span class="badge badge-info">In Progress</span>
                                    <?php elseif ($wo['status'] === 'Under Inspection'): ?>
                                        <span class="badge badge-warning">Under Inspection</span>
                                    <?php elseif ($wo['status'] === 'Awarded'): ?>
                                        <span class="badge badge-primary">Awarded</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($wo['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <form method="POST" action="/construction/work-orders/status/<?= $wo['id'] ?>" style="display: inline-flex; gap: 4px;">
                                        <?= csrf_field() ?>
                                        <select name="status" class="form-control" style="font-size: 12px; padding: 4px 8px; width: auto;" onchange="this.form.submit()">
                                            <option value="Awarded" <?= $wo['status'] === 'Awarded' ? 'selected' : '' ?>>Awarded</option>
                                            <option value="In Progress" <?= $wo['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Under Inspection" <?= $wo['status'] === 'Under Inspection' ? 'selected' : '' ?>>Under Inspection</option>
                                            <option value="Completed" <?= $wo['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="Terminated" <?= $wo['status'] === 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Award Work Order -->
<div id="awardWorkOrderModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div class="erp-card" style="width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; background: #fff; border-radius: 12px; box-shadow: var(--shadow-xl);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="card-title" style="font-size: 17px;">Award Construction Work Contract</h2>
            <button type="button" onclick="document.getElementById('awardWorkOrderModal').style.display='none';" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/construction/work-orders/store" style="padding: 20px;">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Contractor *</label>
                        <select name="contractor_id" class="form-control" required>
                            <option value="">Select Contractor</option>
                            <?php foreach ($contractors as $con): ?>
                                <option value="<?= $con['id'] ?>"><?= esc($con['company_name']) ?> (<?= esc($con['specialization']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Project *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= $prj['id'] ?>"><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Building Tower (Optional)</label>
                        <select name="tower_id" class="form-control">
                            <option value="">All Towers</option>
                            <?php foreach ($towers as $tw): ?>
                                <option value="<?= $tw['id'] ?>"><?= esc($tw['tower_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Milestone Stage (Optional)</label>
                        <select name="milestone_id" class="form-control">
                            <option value="">All Stages</option>
                            <?php foreach ($milestones as $ms): ?>
                                <option value="<?= $ms['id'] ?>"><?= esc($ms['stage_order']) ?>. <?= esc($ms['milestone_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Package / Contract Title *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Structural RCC Framing Package - Tower B" required>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Scope of Work *</label>
                    <textarea name="scope_of_work" class="form-control" rows="3" placeholder="Detailed technical scope of work and deliverables..." required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Contract Amount (₹) *</label>
                        <input type="number" name="contract_amount" class="form-control" placeholder="e.g. 25000000" step="0.01" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Retention Percentage (%)</label>
                        <input type="number" name="retention_percentage" class="form-control" value="5.0" step="0.1" min="0" max="20">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Target Completion Date *</label>
                        <input type="date" name="completion_date" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 4px; display: block;">Payment Terms</label>
                    <textarea name="payment_terms" class="form-control" rows="2" placeholder="e.g. Bi-weekly RA bills with certified cube tests..."></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('awardWorkOrderModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Award Work Order</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
