<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/reports">Reports Hub</a> &rsaquo;
            <span>Rental & Tenancy Report</span>
        </div>
        <h1 class="page-title">Rental Performance & Tenancy Audit</h1>
        <p class="page-subtitle">Tenancy portfolios, lease agreements, monthly rent collections, and security deposits.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Report
        </button>
        <a href="/reports" class="btn btn-secondary">&larr; Back to Hub</a>
    </div>
</div>

<!-- Metrics -->
<?php
$recoveryRate = ($demands > 0) ? round(($collections / $demands) * 100, 1) : 100;
?>
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Active / Total Leases</div>
            <div class="metric-value"><?= count($leases) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Registered Tenancies</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Rent Demanded</div>
            <div class="metric-value" style="color: var(--warning);">₹<?= number_format($demands, 2) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Invoiced rental charges</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Rent Collected</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($collections, 2) ?></div>
            <div class="metric-meta" style="color: var(--success);"><?= $recoveryRate ?>% Collection Efficiency</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
    </div>
</div>

<!-- Leases Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Lease Ref</th>
                    <th>Tenant Details</th>
                    <th>Property & Unit</th>
                    <th>Term Window</th>
                    <th style="text-align: right;">Monthly Rent (₹)</th>
                    <th style="text-align: right;">Security Deposit (₹)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leases)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: var(--slate-500);">
                            No lease records registered in the system.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leases as $l): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary);"><?= esc($l['lease_number'] ?? 'LS-' . $l['id']) ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--slate-900);"><?= esc(($l['tenant_fname'] ?? '') . ' ' . ($l['tenant_lname'] ?? '')) ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 500;"><?= esc($l['property_title'] ?? 'N/A') ?></div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($l['unit_number'] ?? 'N/A') ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;"><?= !empty($l['start_date']) ? date('M d, Y', strtotime($l['start_date'])) : 'N/A' ?> &rarr;</div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= !empty($l['end_date']) ? date('M d, Y', strtotime($l['end_date'])) : 'Ongoing' ?></div>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--slate-800);">
                                ₹<?= number_format((float)($l['rent_amount'] ?? 0), 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--info);">
                                ₹<?= number_format((float)($l['deposit_amount'] ?? 0), 2) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= ($l['status'] ?? 'Active') === 'Active' ? 'success' : 'secondary' ?>">
                                    <?= esc($l['status'] ?? 'Active') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .no-print, .sidebar, .header, .btn {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<?= $this->endSection() ?>
