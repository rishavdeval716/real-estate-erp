<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Onboard and manage civil, MEP, finishing contractors, and trade specialists</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="/construction/work-orders" class="btn btn-outline-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"></rect></svg>
            Work Contracts
        </a>
        <a href="/contractors/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Onboard Contractor
        </a>
    </div>
</div>

<!-- Filters -->
<div class="erp-card" style="margin-bottom: 24px; padding: 20px;">
    <form method="GET" action="/contractors" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 200px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Search Contractor</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name, contact, license..." value="<?= esc($search) ?>">
        </div>
        <div style="flex: 1; min-width: 180px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Specialization</label>
            <select name="specialization" class="form-control" onchange="this.form.submit()">
                <option value="">All Trades</option>
                <option value="Civil & Structural" <?= $selectedSpecialization === 'Civil & Structural' ? 'selected' : '' ?>>Civil & Structural</option>
                <option value="Electrical" <?= $selectedSpecialization === 'Electrical' ? 'selected' : '' ?>>Electrical & MEP</option>
                <option value="Plumbing" <?= $selectedSpecialization === 'Plumbing' ? 'selected' : '' ?>>Plumbing & Fire</option>
                <option value="Finishing & Painting" <?= $selectedSpecialization === 'Finishing & Painting' ? 'selected' : '' ?>>Finishing & Painting</option>
                <option value="HVAC" <?= $selectedSpecialization === 'HVAC' ? 'selected' : '' ?>>HVAC</option>
                <option value="Landscaping" <?= $selectedSpecialization === 'Landscaping' ? 'selected' : '' ?>>Landscaping</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Status</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Active" <?= $selectedStatus === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $selectedStatus === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 16px;">Filter</button>
            <?php if ($search || $selectedSpecialization || $selectedStatus): ?>
                <a href="/contractors" class="btn btn-outline" style="padding: 10px 16px; margin-left: 6px;">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Contractors Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Company Name</th>
                        <th>Trade Specialization</th>
                        <th>Contact Person</th>
                        <th>Phone & Email</th>
                        <th>License / GSTIN</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contractors)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-500); padding: 32px;">No contractors registered.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contractors as $con): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($con['contractor_code']) ?></td>
                                <td style="font-weight: 600; color: var(--slate-900);"><?= esc($con['company_name']) ?></td>
                                <td><span class="badge badge-info"><?= esc($con['specialization']) ?></span></td>
                                <td><?= esc($con['contact_person']) ?></td>
                                <td style="font-size: 13px;">
                                    <div><?= esc($con['phone']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($con['email']) ?></div>
                                </td>
                                <td style="font-size: 12px; color: var(--slate-600);">
                                    <div><?= esc($con['license_number'] ?: '-') ?></div>
                                    <div style="font-size: 11px; color: var(--slate-400);"><?= esc($con['gstin'] ?: '') ?></div>
                                </td>
                                <td>
                                    <span style="color: var(--warning); font-weight: 700;">★ <?= number_format((float)$con['rating'], 1) ?></span>
                                </td>
                                <td>
                                    <?php if ($con['status'] === 'Active'): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?= esc($con['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/contractors/edit/<?= $con['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <?php if ($con['status'] === 'Active'): ?>
                                        <form method="POST" action="/contractors/delete/<?= $con['id'] ?>" style="display: inline;" onsubmit="return confirm('Deactivate this contractor?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Deactivate</button>
                                        </form>
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
