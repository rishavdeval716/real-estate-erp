<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/owners">Property Owners</a> &rsaquo;
            <span><?= esc($owner['owner_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($owner['first_name'] . ' ' . $owner['last_name']) ?></h1>
        <p class="page-subtitle">Owner Code: <?= esc($owner['owner_code']) ?> <?= $owner['company_name'] ? '&bull; ' . esc($owner['company_name']) : '' ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/owners/edit/<?= $owner['id'] ?>" class="btn btn-secondary">Edit Profile</a>
        <a href="/owners/statement/<?= $owner['id'] ?>" class="btn btn-primary" target="_blank">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="6" x2="6" y1="2" y2="22"/><line x1="18" x2="18" y1="2" y2="22"/><rect width="20" height="16" x="2" y="4" rx="2"/></svg>
            Print Portfolio Statement
        </a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Properties Owned</div>
            <div class="metric-value"><?= number_format($owner['total_properties']) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Portfolio assets</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Rental Income</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($owner['rental_income'], 2) ?></div>
            <div class="metric-meta" style="color: var(--success);">Collected returns</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Incurred Expenses</div>
            <div class="metric-value" style="color: var(--warning);">₹<?= number_format($owner['expenses'], 2) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Maintenance & taxes</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Net Yield Balance</div>
            <div class="metric-value" style="color: var(--info);">₹<?= number_format($owner['rental_income'] - $owner['expenses'], 2) ?></div>
            <div class="metric-meta" style="color: var(--info);">Disbursable surplus</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>
</div>

<div class="form-row" style="margin-bottom: 1.5rem;">
    <!-- Profile Card -->
    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Owner Profile & KYC Credentials</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Full Legal Name</td>
                        <td><strong><?= esc($owner['first_name'] . ' ' . $owner['last_name']) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Corporate Entity</td>
                        <td><?= esc($owner['company_name'] ?: 'Individual Landlord') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Email Address</td>
                        <td><?= esc($owner['email']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Phone / Mobile</td>
                        <td><?= esc($owner['phone']) ?><?= $owner['alternate_phone'] ? ' / ' . esc($owner['alternate_phone']) : '' ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Address</td>
                        <td><?= esc($owner['address'] ?: 'N/A') ?>, <?= esc($owner['city']) ?>, <?= esc($owner['state']) ?> <?= esc($owner['pincode']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">KYC Status</td>
                        <td>
                            <span class="badge <?= $owner['kyc_status'] === 'verified' ? 'badge-success' : 'badge-warning' ?>">
                                <?= ucfirst(esc($owner['kyc_status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">PAN Number</td>
                        <td><?= esc($owner['pan_number'] ?: '—') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Aadhaar Number</td>
                        <td><?= esc($owner['aadhaar_number'] ?: '—') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Banking & Payout Credentials -->
    <div class="form-col-6">
        <div class="card" style="height: 100%;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Disbursement & Banking Details</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <table class="table" style="font-size: 0.88rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Bank Name</td>
                        <td><strong><?= esc($owner['bank_name'] ?: 'Not Configured') ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Account Number</td>
                        <td><?= esc($owner['bank_account_number'] ?: '—') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">IFSC Code</td>
                        <td><?= esc($owner['bank_ifsc'] ?: '—') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Account Status</td>
                        <td>
                            <span class="badge <?= $owner['status'] === 'active' ? 'badge-success' : 'badge-secondary' ?>">
                                <?= ucfirst(esc($owner['status'])) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Notes</td>
                        <td><?= esc($owner['notes'] ?: 'No special notes recorded.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Associated Properties Portfolio Table -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Associated Properties Portfolio (<?= count($owner['properties']) ?>)</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Property Code</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Area</th>
                        <th>Market Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($owner['properties'])): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--slate-500);">No properties associated with this owner yet.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($owner['properties'] as $p): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($p['property_code']) ?></strong></td>
                        <td><?= esc($p['title']) ?></td>
                        <td><?= esc($p['project_name'] ?: 'Independent') ?></td>
                        <td><?= esc($p['property_type_name'] ?? 'Residential') ?></td>
                        <td><?= esc($p['location_city'] ?: 'Prime City') ?></td>
                        <td><?= number_format((float)$p['area'], 0) ?> Sq.Ft.</td>
                        <td><strong>₹<?= number_format((float)$p['price'], 2) ?></strong></td>
                        <td><span class="badge badge-success"><?= esc($p['status']) ?></span></td>
                        <td style="text-align: right;">
                            <a href="/properties/view/<?= $p['id'] ?>" class="btn btn-sm btn-secondary">View Property</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Associated Compliance & Title Documents -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Ownership & Title Documents (<?= count($owner['documents']) ?>)</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doc Code</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>File Name</th>
                        <th>Verification Status</th>
                        <th>Issue Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($owner['documents'])): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">No title or compliance documents recorded for this owner.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($owner['documents'] as $d): ?>
                    <tr>
                        <td><strong><?= esc($d['document_code']) ?></strong></td>
                        <td><?= esc($d['title']) ?></td>
                        <td><?= esc($d['document_category']) ?></td>
                        <td><?= esc($d['file_name']) ?></td>
                        <td>
                            <span class="badge <?= $d['verification_status'] === 'Verified' ? 'badge-success' : 'badge-warning' ?>">
                                <?= esc($d['verification_status']) ?>
                            </span>
                        </td>
                        <td><?= $d['issue_date'] ? date('d M Y', strtotime($d['issue_date'])) : '—' ?></td>
                        <td style="text-align: right;">
                            <a href="/documents/view/<?= $d['id'] ?>" class="btn btn-sm btn-secondary">View Document</a>
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
