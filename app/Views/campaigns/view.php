<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/campaigns">Marketing Campaigns</a> &rsaquo;
            <span><?= esc($campaign['campaign_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($campaign['name']) ?></h1>
        <p class="page-subtitle"><?= esc($campaign['campaign_type']) ?> &bull; Code: <?= esc($campaign['campaign_code']) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/campaigns/edit/<?= $campaign['id'] ?>" class="btn btn-secondary">Edit Campaign</a>
        <a href="/campaigns" class="btn btn-secondary">Back to Campaigns</a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Cost Per Lead (CPL)</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format($cpl, 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Acquisition efficiency</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Conversion Rate</div>
            <div class="metric-value" style="color: var(--success);"><?= $conversionRate ?>%</div>
            <div class="metric-meta" style="color: var(--success);"><?= $campaign['converted_leads'] ?> Final Bookings</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Budget Utilization</div>
            <?php 
            $budgetPct = $campaign['budget'] > 0 ? round(($campaign['actual_spend'] / $campaign['budget']) * 100, 1) : 0;
            ?>
            <div class="metric-value"><?= $budgetPct ?>%</div>
            <div class="metric-meta" style="color: var(--slate-500);">₹<?= number_format((float)$campaign['actual_spend'], 2) ?> / ₹<?= number_format((float)$campaign['budget'], 2) ?></div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Lead Funnel</div>
            <div class="metric-value"><?= $campaign['leads_generated'] ?></div>
            <div class="metric-meta" style="color: var(--slate-500);"><?= $campaign['qualified_leads'] ?> Qualified Inquiries</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Campaign Strategy & Setup</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Channel Type</td>
                        <td><span class="badge badge-info"><?= esc($campaign['campaign_type']) ?></span></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Status</td>
                        <td>
                            <span class="badge <?= $campaign['status'] === 'Active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= esc($campaign['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Target Project</td>
                        <td><?= esc($project['name'] ?? 'Not Tied to Single Project') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Target Property</td>
                        <td><?= esc($property['title'] ?? 'General Portfolio') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Campaign Period</td>
                        <td>
                            <?= date('d M Y', strtotime($campaign['start_date'])) ?>
                            <?= $campaign['end_date'] ? ' to ' . date('d M Y', strtotime($campaign['end_date'])) : ' (Ongoing)' ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Strategy Copy</td>
                        <td><?= esc($campaign['description'] ?: 'No strategy notes provided.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Financial ROI Analysis</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Budget Allocation</td>
                        <td><strong>₹<?= number_format((float)$campaign['budget'], 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Actual Spend</td>
                        <td><strong style="color: var(--danger);">₹<?= number_format((float)$campaign['actual_spend'], 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Total Ingestion</td>
                        <td><?= $campaign['leads_generated'] ?> Leads</td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Qualified Inquiries</td>
                        <td><?= $campaign['qualified_leads'] ?> Leads</td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Final Conversions</td>
                        <td><strong style="color: var(--success);"><?= $campaign['converted_leads'] ?> Units Booked</strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Internal Notes</td>
                        <td><?= esc($campaign['notes'] ?: 'No remarks logged.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
