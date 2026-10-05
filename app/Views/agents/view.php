<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/agents">Agents & Brokers</a> &rsaquo;
            <span><?= esc($agent['agent_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?></h1>
        <p class="page-subtitle"><?= esc($agent['agent_type']) ?> <?= $agent['agency_name'] ? '&bull; ' . esc($agent['agency_name']) : '' ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/agents/edit/<?= $agent['id'] ?>" class="btn btn-secondary">Edit Agent</a>
        <a href="/commissions" class="btn btn-primary">Commissions Ledger</a>
    </div>
</div>

<!-- Performance Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Commission Earned</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format($agent['total_commission_earned'], 2) ?></div>
            <div class="metric-meta" style="color: var(--primary);">Approved commissions</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Commission Paid</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($agent['total_commission_paid'], 2) ?></div>
            <div class="metric-meta" style="color: var(--success);">Disbursed to date</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Assigned Leads</div>
            <div class="metric-value"><?= number_format($agent['leads_count']) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);"><?= $agent['converted_leads'] ?> Converted Sales</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Sales Conversion</div>
            <div class="metric-value" style="color: var(--warning);"><?= $agent['conversion_rate'] ?>%</div>
            <div class="metric-meta" style="color: var(--warning);">Win rate efficiency</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
    </div>
</div>

<div class="form-row" style="margin-bottom: 1.5rem;">
    <!-- Profile Card -->
    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Broker Credentials</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Agent Code</td>
                        <td><strong style="color: var(--primary);"><?= esc($agent['agent_code']) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Channel Type</td>
                        <td><span class="badge badge-info"><?= esc($agent['agent_type']) ?></span></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Agency / Firm</td>
                        <td><?= esc($agent['agency_name'] ?: 'Independent Broker') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">RERA Registration</td>
                        <td><code><?= esc($agent['license_number'] ?: 'Unregistered') ?></code></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Income Tax PAN</td>
                        <td><?= esc($agent['pan_number'] ?: '—') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Commission Rate</td>
                        <td><strong style="color: var(--success);"><?= number_format((float)$agent['commission_rate'], 2) ?>%</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Contact & Status -->
    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Contact & Operating Status</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Email Address</td>
                        <td><?= esc($agent['email']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Phone / Mobile</td>
                        <td><?= esc($agent['phone']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Office Location</td>
                        <td><?= esc($agent['city'] ?: 'N/A') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Account Status</td>
                        <td>
                            <span class="badge <?= $agent['status'] === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst(esc($agent['status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Activity Notes</td>
                        <td><?= esc($agent['notes'] ?: 'No notes recorded.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Commission Ledger Table -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Commission & Payout History (<?= count($agent['commissions']) ?>)</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Customer</th>
                        <th>Booking Consideration</th>
                        <th>Commission Amount</th>
                        <th>Payout Status</th>
                        <th>Payable Date</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agent['commissions'])): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">No commissions registered for this broker yet.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($agent['commissions'] as $c): ?>
                    <tr>
                        <td><strong><?= esc($c['booking_number']) ?></strong></td>
                        <td><?= esc($c['cust_fname'] . ' ' . $c['cust_lname']) ?></td>
                        <td>₹<?= number_format((float)$c['booking_amount'], 2) ?></td>
                        <td><strong style="color: var(--primary);">₹<?= number_format((float)$c['commission_amount'], 2) ?></strong></td>
                        <td>
                            <span class="badge <?= $c['status'] === 'Paid' ? 'badge-success' : ($c['status'] === 'Approved' ? 'badge-info' : 'badge-warning') ?>">
                                <?= esc($c['status']) ?>
                            </span>
                        </td>
                        <td><?= $c['payable_date'] ? date('d M Y', strtotime($c['payable_date'])) : '—' ?></td>
                        <td><?= $c['paid_date'] ? date('d M Y', strtotime($c['paid_date'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
