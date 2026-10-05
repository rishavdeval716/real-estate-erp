<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/reports/receivables">Compliance & Reports</a> &rsaquo;
            <span>Accounts Receivable</span>
        </div>
        <h1 class="page-title">Accounts Receivable & Ageing Analysis</h1>
        <p class="page-subtitle">Real-time ledger of overdue sales invoices, rent demands, and construction payment milestones categorized into ageing buckets.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Ageing Schedule
        </button>
    </div>
</div>

<!-- Ageing Buckets Summary Banner -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Total Receivables</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--slate-900); margin-top: 0.25rem;">
            ₹<?= number_format((float)$totalReceivables, 2) ?>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Current (Not Overdue)</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--emerald-600); margin-top: 0.25rem;">
            ₹<?= number_format((float)$buckets['current'], 2) ?>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">1 &ndash; 30 Days</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #3b82f6; margin-top: 0.25rem;">
            ₹<?= number_format((float)$buckets['days_30'], 2) ?>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">31 &ndash; 60 Days</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--amber-600); margin-top: 0.25rem;">
            ₹<?= number_format((float)$buckets['days_60'], 2) ?>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">61 &ndash; 90 Days</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #f97316; margin-top: 0.25rem;">
            ₹<?= number_format((float)$buckets['days_90'], 2) ?>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">90+ Days (Critical)</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--rose-600); margin-top: 0.25rem;">
            ₹<?= number_format((float)$buckets['over_90'], 2) ?>
        </div>
    </div>
</div>

<!-- Detailed Receivables Ledger -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Comprehensive Outstanding Accounts Ledger</h3>
        <span class="badge badge-light"><?= count($receivables) ?> Outstanding Records</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Source / Type</th>
                        <th>Reference #</th>
                        <th>Debtor / Customer / Tenant</th>
                        <th>Contact</th>
                        <th>Due Date</th>
                        <th>Days Overdue</th>
                        <th>Ageing Category</th>
                        <th style="text-align: right;">Outstanding Due (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receivables)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No outstanding receivables due. All customer and tenant accounts are balanced.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($receivables as $rec): ?>
                    <tr>
                        <td>
                            <span class="badge badge-light" style="font-weight: 600;"><?= esc($rec['type']) ?></span>
                        </td>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($rec['reference']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-800);"><?= esc($rec['party_name']) ?></div>
                        </td>
                        <td><?= esc($rec['contact']) ?></td>
                        <td><?= date('d M Y', strtotime($rec['due_date'])) ?></td>
                        <td>
                            <?php if ($rec['days_overdue'] > 0): ?>
                            <span style="font-weight: 700; color: var(--rose-600);"><?= esc($rec['days_overdue']) ?> days</span>
                            <?php else: ?>
                            <span style="color: var(--emerald-600); font-weight: 500;">Current (On schedule)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $bBadge = match($rec['bucket']) {
                                'Current'    => 'badge-success',
                                '1-30 Days'  => 'badge-info',
                                '31-60 Days' => 'badge-warning',
                                default      => 'badge-danger',
                            };
                            ?>
                            <span class="badge <?= $bBadge ?>"><?= esc($rec['bucket']) ?></span>
                        </td>
                        <td style="text-align: right;">
                            <strong style="font-size: 1rem; color: var(--slate-900);">₹<?= number_format((float)$rec['amount'], 2) ?></strong>
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
