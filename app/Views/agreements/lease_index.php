<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Lease Agreements</span>
        </div>
        <h1 class="page-title">Lease Agreements</h1>
        <p class="page-subtitle">Manage residential and commercial leasing contracts, renewals, and lock-in terms.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/leases/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Draft New Lease
        </a>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Leases</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Active Leases</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['active']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Expiring Soon (< 60d)</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['expiring_soon']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Expired / Terminated</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);"><?= esc($kpi['expired']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/leases" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Agreement #, tenant name, code, property..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Agreement Type</label>
                <select name="agreement_type" class="form-control">
                    <option value="">All Types</option>
                    <option value="residential" <?= $filters['agreement_type'] === 'residential' ? 'selected' : '' ?>>Residential</option>
                    <option value="commercial" <?= $filters['agreement_type'] === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="draft" <?= $filters['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="expiring_soon" <?= $filters['status'] === 'expiring_soon' ? 'selected' : '' ?>>Expiring Soon</option>
                    <option value="expired" <?= $filters['status'] === 'expired' ? 'selected' : '' ?>>Expired</option>
                    <option value="terminated" <?= $filters['status'] === 'terminated' ? 'selected' : '' ?>>Terminated</option>
                    <option value="renewed" <?= $filters['status'] === 'renewed' ? 'selected' : '' ?>>Renewed</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/leases" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Leases Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Agreement Number</th>
                        <th>Tenant</th>
                        <th>Property & Unit</th>
                        <th>Type</th>
                        <th>Term Dates</th>
                        <th>Monthly Rent</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leases)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.5rem; display: block; margin-left: auto; margin-right: auto; opacity: 0.5;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            No lease agreements found matching current criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($leases as $lease): ?>
                    <tr>
                        <td>
                            <a href="/leases/view/<?= $lease['id'] ?>" style="font-weight: 600; color: var(--primary-600);">
                                <?= esc($lease['agreement_number']) ?>
                            </a>
                            <div style="font-size: 0.75rem; color: var(--slate-400);">Lock-in: <?= (int)$lease['lock_in_period_months'] ?> mos</div>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($lease['tenant_name']) ?></div>
                            <span class="badge badge-light" style="font-size: 0.7rem;"><?= esc($lease['tenant_code']) ?></span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($lease['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">
                                Unit: <?= esc($lease['unit_number'] ?? 'Whole Property') ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $lease['agreement_type'] === 'commercial' ? 'badge-info' : 'badge-light' ?>" style="text-transform: capitalize;">
                                <?= esc($lease['agreement_type']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 500;"><?= date('d M Y', strtotime($lease['start_date'])) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">to <?= date('d M Y', strtotime($lease['end_date'])) ?></div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--slate-800);">₹<?= number_format((float)$lease['monthly_rent'], 2) ?></span>
                            <?php if ((float)$lease['rent_escalation_pct'] > 0): ?>
                            <div style="font-size: 0.75rem; color: var(--emerald-600);">+<?= esc($lease['rent_escalation_pct']) ?>% escalation</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="color: var(--slate-700);">₹<?= number_format((float)$lease['security_deposit'], 2) ?></span>
                        </td>
                        <td>
                            <?php
                            $badgeClass = match($lease['status']) {
                                'active'        => 'badge-success',
                                'expiring_soon' => 'badge-warning',
                                'draft'         => 'badge-secondary',
                                'renewed'       => 'badge-info',
                                'expired', 'terminated' => 'badge-danger',
                                default         => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($lease['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="/leases/view/<?= $lease['id'] ?>" class="btn btn-sm btn-secondary" title="View Lease Details">View</a>
                            <a href="/leases/voucher/<?= $lease['id'] ?>" class="btn btn-sm btn-secondary" title="Print Agreement Document" target="_blank">Print</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
    <div class="card-footer" style="padding: 1rem;">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
