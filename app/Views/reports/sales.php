<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/reports">Reports Hub</a> &rsaquo;
            <span>Sales & Revenue Report</span>
        </div>
        <h1 class="page-title">Property Sales & Revenue Turnover Report</h1>
        <p class="page-subtitle">Fiscal year analysis of property sales contracts, token realizations, and gross consideration.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Report
        </button>
        <a href="/reports" class="btn btn-secondary">&larr; Back to Hub</a>
    </div>
</div>

<!-- Year Filter -->
<div class="card no-print" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/reports/sales" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="min-width: 180px;">
                <label class="form-label">Fiscal Year</label>
                <select name="year" class="form-control" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Refresh Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Bookings Recorded</div>
            <div class="metric-value"><?= count($bookings) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Year <?= esc($year) ?> Contracts</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Gross Sales Turnover</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($totalSales, 2) ?></div>
            <div class="metric-meta" style="color: var(--success);">Aggregate deal value</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Token Advances Realized</div>
            <div class="metric-value" style="color: var(--info);">₹<?= number_format($totalTokens, 2) ?></div>
            <div class="metric-meta" style="color: var(--info);">Initial earnest deposits</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
    </div>
</div>

<!-- Sales Detail Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking # / Date</th>
                    <th>Customer Name</th>
                    <th>Property & Unit</th>
                    <th style="text-align: right;">Token Paid (₹)</th>
                    <th style="text-align: right;">Total Consideration (₹)</th>
                    <th>Booking Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--slate-500);">
                            No sales transactions recorded for year <?= esc($year) ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary);"><?= esc($b['booking_number'] ?? 'BKG-' . $b['id']) ?></strong>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= date('M d, Y', strtotime($b['booking_date'])) ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--slate-900);"><?= esc(($b['first_name'] ?? '') . ' ' . ($b['last_name'] ?? '')) ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 500;"><?= esc($b['property_title'] ?? 'N/A') ?></div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($b['unit_number'] ?? 'N/A') ?></div>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--info);">
                                ₹<?= number_format((float)($b['token_amount'] ?? 0), 2) ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--success);">
                                ₹<?= number_format((float)($b['total_amount'] ?? 0), 2) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= ($b['booking_status'] === 'Confirmed' || $b['booking_status'] === 'Completed') ? 'success' : ($b['booking_status'] === 'Cancelled' ? 'danger' : 'warning') ?>">
                                    <?= esc($b['booking_status'] ?? 'Pending') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($bookings)): ?>
                <tfoot>
                    <tr style="background: var(--slate-50); font-weight: 700;">
                        <td colspan="3">Total Turnover For <?= esc($year) ?></td>
                        <td style="text-align: right; color: var(--info);">₹<?= number_format($totalTokens, 2) ?></td>
                        <td style="text-align: right; color: var(--success);">₹<?= number_format($totalSales, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
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
