<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Property Owners</span>
        </div>
        <h1 class="page-title">Property Owners Management</h1>
        <p class="page-subtitle">Manage property landlords, ownership portfolios, KYC verification, and financial statements.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/owners/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Register New Owner
        </a>
    </div>
</div>

<!-- Metrics Grid -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Landlords</div>
            <div class="metric-value"><?= number_format($totalOwners) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Registered portfolio owners</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Verified KYC</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($verifiedOwners) ?></div>
            <div class="metric-meta" style="color: var(--success);">Fully verified compliance</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Pending Verification</div>
            <div class="metric-value" style="color: var(--warning);"><?= number_format($pendingKyc) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Action required</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/owners" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, code, company, email, phone..." value="<?= esc($search) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">KYC Status</label>
                <select name="kyc_status" class="form-control">
                    <option value="">All KYC Statuses</option>
                    <option value="pending" <?= $kycStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="verified" <?= $kycStatus === 'verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="rejected" <?= $kycStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Account Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/owners" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Owners Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Owner Code</th>
                        <th>Owner Name & Company</th>
                        <th>Contact Information</th>
                        <th>City / State</th>
                        <th>PAN / Aadhaar</th>
                        <th>KYC Status</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($owners)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No property owners found matching your criteria.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($owners as $o): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary);"><?= esc($o['owner_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);">
                                <?= esc($o['first_name'] . ' ' . $o['last_name']) ?>
                            </div>
                            <?php if (!empty($o['company_name'])): ?>
                            <div style="font-size: 0.78rem; color: var(--slate-500);"><?= esc($o['company_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div><?= esc($o['phone']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--slate-500);"><?= esc($o['email']) ?></div>
                        </td>
                        <td>
                            <?= esc($o['city'] ?: 'N/A') ?><?= $o['state'] ? ', ' . esc($o['state']) : '' ?>
                        </td>
                        <td>
                            <div>PAN: <?= esc($o['pan_number'] ?: '—') ?></div>
                            <?php if ($o['aadhaar_number']): ?>
                            <div style="font-size: 0.78rem; color: var(--slate-500);">UID: <?= esc($o['aadhaar_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $o['kyc_status'] === 'verified' ? 'badge-success' : ($o['kyc_status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                                <?= ucfirst(esc($o['kyc_status'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $o['status'] === 'active' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= ucfirst(esc($o['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.4rem;">
                                <a href="/owners/view/<?= $o['id'] ?>" class="btn btn-sm btn-secondary" title="View Portfolio">View</a>
                                <a href="/owners/edit/<?= $o['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Owner">Edit</a>
                                <a href="/owners/statement/<?= $o['id'] ?>" class="btn btn-sm btn-secondary" title="Statement" target="_blank">Statement</a>
                            </div>
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
