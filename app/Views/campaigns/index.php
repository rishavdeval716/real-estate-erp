<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Marketing Campaigns</span>
        </div>
        <h1 class="page-title">Marketing & Advertisement Management</h1>
        <p class="page-subtitle">Track multi-channel promotional campaigns, property portal listings, acquisition spend, and lead conversion rates.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/campaigns/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Launch Campaign
        </a>
    </div>
</div>

<!-- Dashboard Metrics (9 Required Metrics from DB) -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Campaigns</div>
            <div class="metric-value"><?= number_format($metrics['total_campaigns']) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);"><?= $metrics['active_campaigns'] ?> Currently Active</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Campaign Budget</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format($metrics['total_budget'], 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Allocated capital</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Actual Spend</div>
            <div class="metric-value" style="color: var(--danger);">₹<?= number_format($metrics['total_spend'], 2) ?></div>
            <div class="metric-meta" style="color: var(--danger);">Utilized spend</div>
        </div>
        <div class="metric-icon-box" style="background: var(--danger-light); color: var(--danger);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Leads Generated</div>
            <div class="metric-value"><?= number_format($metrics['total_leads']) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);"><?= $metrics['total_qualified'] ?> Qualified Inquiries</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Conversions</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($metrics['total_conversions']) ?></div>
            <div class="metric-meta" style="color: var(--success);">Finalized bookings</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Cost Per Lead (CPL)</div>
            <div class="metric-value">₹<?= number_format($metrics['cost_per_lead'], 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Average acquisition cost</div>
        </div>
        <div class="metric-icon-box" style="background: var(--slate-100); color: var(--slate-700);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Conversion Rate</div>
            <div class="metric-value" style="color: var(--success);"><?= $metrics['conversion_rate'] ?>%</div>
            <div class="metric-meta" style="color: var(--success);">Lead to booking ratio</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/campaigns" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search campaign name, code..." value="<?= esc($search) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Campaign Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="Social Media" <?= $type === 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                    <option value="Google Ads" <?= $type === 'Google Ads' ? 'selected' : '' ?>>Google Ads</option>
                    <option value="Property Portal" <?= $type === 'Property Portal' ? 'selected' : '' ?>>Property Portal</option>
                    <option value="Hoarding / Outdoor" <?= $type === 'Hoarding / Outdoor' ? 'selected' : '' ?>>Hoarding / Outdoor</option>
                    <option value="Print Media" <?= $type === 'Print Media' ? 'selected' : '' ?>>Print Media</option>
                    <option value="Email Marketing" <?= $type === 'Email Marketing' ? 'selected' : '' ?>>Email Marketing</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Planning" <?= $status === 'Planning' ? 'selected' : '' ?>>Planning</option>
                    <option value="Paused" <?= $status === 'Paused' ? 'selected' : '' ?>>Paused</option>
                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/campaigns" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Campaigns Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Campaign Code</th>
                        <th>Campaign Name</th>
                        <th>Channel Type</th>
                        <th>Timeline</th>
                        <th>Budget</th>
                        <th>Actual Spend</th>
                        <th>Leads / Qualified</th>
                        <th>Conversions</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No campaigns found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($campaigns as $c): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($c['campaign_code']) ?></strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($c['name']) ?></div>
                        </td>
                        <td><span class="badge badge-info"><?= esc($c['campaign_type']) ?></span></td>
                        <td>
                            <div style="font-size: 0.82rem;">
                                <?= date('d M Y', strtotime($c['start_date'])) ?>
                                <?= $c['end_date'] ? '&rarr; ' . date('d M Y', strtotime($c['end_date'])) : '' ?>
                            </div>
                        </td>
                        <td>₹<?= number_format((float)$c['budget'], 2) ?></td>
                        <td><strong style="color: var(--danger);">₹<?= number_format((float)$c['actual_spend'], 2) ?></strong></td>
                        <td>
                            <strong><?= $c['leads_generated'] ?></strong>
                            <span style="font-size: 0.78rem; color: var(--slate-500);">(<?= $c['qualified_leads'] ?> Qual.)</span>
                        </td>
                        <td>
                            <strong style="color: var(--success);"><?= $c['converted_leads'] ?></strong>
                        </td>
                        <td>
                            <span class="badge <?= $c['status'] === 'Active' ? 'badge-success' : ($c['status'] === 'Completed' ? 'badge-secondary' : 'badge-warning') ?>">
                                <?= esc($c['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.4rem;">
                                <a href="/campaigns/view/<?= $c['id'] ?>" class="btn btn-sm btn-secondary">Analytics</a>
                                <a href="/campaigns/edit/<?= $c['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
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
