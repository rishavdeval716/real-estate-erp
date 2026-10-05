<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/tenants">Rentals</a> &rsaquo;
            <span>Tenants</span>
        </div>
        <h1 class="page-title">Tenant Directory</h1>
        <p class="page-subtitle">Manage leaseholders, corporate tenants, KYC verification, and occupied inventory.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/tenants/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Tenant
        </a>
    </div>
</div>

<!-- Dynamic KPI Metrics -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Tenants</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Active Tenancies</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['active']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">KYC Verified</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--indigo-600);"><?= esc($kpi['kyc_verified']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">KYC Pending</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['kyc_pending']) ?></div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/tenants" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, company, code, email, mobile..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Tenant Type</label>
                <select name="tenant_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="individual" <?= $filters['tenant_type'] === 'individual' ? 'selected' : '' ?>>Individual</option>
                    <option value="company" <?= $filters['tenant_type'] === 'company' ? 'selected' : '' ?>>Company</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">KYC Status</label>
                <select name="kyc_status" class="form-control">
                    <option value="">All KYC Statuses</option>
                    <option value="pending" <?= $filters['kyc_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="verified" <?= $filters['kyc_status'] === 'verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="rejected" <?= $filters['kyc_status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="blacklisted" <?= $filters['status'] === 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/tenants" class="btn btn-secondary" title="Reset Filters">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tenants Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Occupied Property / Unit</th>
                        <th>KYC Status</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tenants)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                No tenant records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tenants as $t): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-800);">
                                        <a href="/tenants/view/<?= $t['id'] ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($t['full_name']) ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500); font-family: monospace;">
                                        <?= esc($t['tenant_code']) ?>
                                        <?php if (!empty($t['company_name'])): ?>
                                            &bull; <?= esc($t['company_name']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $t['tenant_type'] === 'company' ? 'badge-info' : 'badge-secondary' ?>">
                                        <?= ucfirst(esc($t['tenant_type'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div><?= esc($t['mobile']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($t['email']) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($t['property_title'])): ?>
                                        <div style="font-weight: 500;"><?= esc($t['property_title']) ?></div>
                                        <?php if (!empty($t['unit_number'])): ?>
                                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit <?= esc($t['unit_number']) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.85rem;">None assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $kycClass = match ($t['kyc_status']) {
                                        'verified' => 'badge-success',
                                        'rejected' => 'badge-danger',
                                        default    => 'badge-warning',
                                    };
                                    ?>
                                    <span class="badge <?= $kycClass ?>"><?= ucfirst(esc($t['kyc_status'])) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = match ($t['status']) {
                                        'active'      => 'badge-success',
                                        'blacklisted' => 'badge-danger',
                                        default       => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst(esc($t['status'])) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                        <a href="/tenants/view/<?= $t['id'] ?>" class="btn btn-sm btn-secondary" title="View Profile">
                                            View
                                        </a>
                                        <a href="/tenants/edit/<?= $t['id'] ?>" class="btn btn-sm btn-secondary" title="Edit">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pager)): ?>
            <div style="padding: 1rem;">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
