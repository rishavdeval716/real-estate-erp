<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Material indent requests, cement, steel, masonry procurement, and approval workflow</p>
    </div>
    <div>
        <a href="/procurement/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create Material Indent
        </a>
    </div>
</div>

<!-- Filters -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/procurement" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
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
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Status</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Requested" <?= $selectedStatus === 'Requested' ? 'selected' : '' ?>>Requested</option>
                <option value="Approved" <?= $selectedStatus === 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Procured" <?= $selectedStatus === 'Procured' ? 'selected' : '' ?>>Procured</option>
                <option value="Rejected" <?= $selectedStatus === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>
        <?php if ($selectedProject || $selectedStatus): ?>
            <div>
                <a href="/procurement" class="btn btn-outline" style="padding: 10px 16px;">Reset</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Requisitions Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Req Code</th>
                        <th>Material Item</th>
                        <th>Category</th>
                        <th>Project & Tower</th>
                        <th>Quantity</th>
                        <th>Est. Rate & Total</th>
                        <th>Required By</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requisitions)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--slate-500); padding: 32px;">No material requisitions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requisitions as $req): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($req['requisition_code']) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($req['item_name']) ?></div>
                                    <?php if ($req['remarks']): ?>
                                        <div style="font-size: 11px; color: var(--slate-500);"><?= esc($req['remarks']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-secondary"><?= esc($req['category']) ?></span></td>
                                <td>
                                    <div><?= esc($req['project_name']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($req['tower_name'] ?? 'Main Site') ?></div>
                                </td>
                                <td><strong><?= number_format((float)$req['quantity'], 2) ?></strong> <?= esc($req['unit_of_measure']) ?></td>
                                <td>
                                    <div>₹<?= number_format((float)$req['estimated_unit_cost'], 2) ?> / <?= esc($req['unit_of_measure']) ?></div>
                                    <div style="font-weight: 700; color: var(--slate-900);">₹<?= number_format((float)$req['estimated_total_cost'], 2) ?></div>
                                </td>
                                <td style="font-size: 13px;"><?= date('d M Y', strtotime($req['required_by_date'])) ?></td>
                                <td>
                                    <?php if ($req['priority'] === 'Urgent'): ?>
                                        <span class="badge badge-danger">Urgent</span>
                                    <?php elseif ($req['priority'] === 'High'): ?>
                                        <span class="badge badge-warning">High</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($req['priority']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($req['status'] === 'Procured'): ?>
                                        <span class="badge badge-success">Procured</span>
                                    <?php elseif ($req['status'] === 'Approved'): ?>
                                        <span class="badge badge-info">Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?= esc($req['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($req['status'] === 'Requested'): ?>
                                        <form method="POST" action="/procurement/approve/<?= $req['id'] ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Approve</button>
                                        </form>
                                    <?php elseif ($req['status'] === 'Approved'): ?>
                                        <form method="POST" action="/procurement/procure/<?= $req['id'] ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-success">Mark Procured</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--slate-400);">Completed</span>
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
