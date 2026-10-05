<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Verifications</span>
        </div>
        <h1 class="page-title">Property Verification & Compliance Audits</h1>
        <p class="page-subtitle">Schedule, audit, and track 30-year legal title searches, RERA compliance checks, municipal clearances, and physical site audits.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/verifications/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Schedule Compliance Audit
        </a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Audits</div>
            <div class="metric-value"><?= number_format($totalAudits) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Compliance inspections</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Verified Compliant</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($verifiedAudits) ?></div>
            <div class="metric-meta" style="color: var(--success);">Zero defect clearance</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Under Active Review</div>
            <div class="metric-value" style="color: var(--warning);"><?= number_format($underReview) ?></div>
            <div class="metric-meta" style="color: var(--warning);">In legal pipeline</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/verifications" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search verification code, property title..." value="<?= esc($search) ?>">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Audit Type</label>
                <select name="type" class="form-control">
                    <option value="">All Verification Types</option>
                    <option value="Legal Title Clearance" <?= $type === 'Legal Title Clearance' ? 'selected' : '' ?>>Legal Title Clearance</option>
                    <option value="Physical Property Audit" <?= $type === 'Physical Property Audit' ? 'selected' : '' ?>>Physical Property Audit</option>
                    <option value="RERA Compliance Check" <?= $type === 'RERA Compliance Check' ? 'selected' : '' ?>>RERA Compliance Check</option>
                    <option value="Municipal Approval Check" <?= $type === 'Municipal Approval Check' ? 'selected' : '' ?>>Municipal Approval Check</option>
                    <option value="Structural & Fire Safety" <?= $type === 'Structural & Fire Safety' ? 'selected' : '' ?>>Structural & Fire Safety</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Verified" <?= $status === 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Under Review" <?= $status === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="Expired" <?= $status === 'Expired' ? 'selected' : '' ?>>Expired</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/verifications" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Verifications Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Audit Code</th>
                        <th>Target Property</th>
                        <th>Verification Type</th>
                        <th>Findings & Checkpoints</th>
                        <th>Audit Verdict</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($verifications)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No verification records found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($verifications as $v): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($v['verification_code']) ?></strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($v['property_title']) ?></div>
                            <div style="font-size: 0.78rem; color: var(--slate-500);"><?= esc($v['property_code']) ?></div>
                        </td>
                        <td><span class="badge badge-info"><?= esc($v['verification_type']) ?></span></td>
                        <td>
                            <div style="font-size: 0.85rem; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= esc($v['findings'] ?: 'Audit in progress...') ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $v['status'] === 'Verified' ? 'badge-success' : ($v['status'] === 'Under Review' ? 'badge-warning' : ($v['status'] === 'Pending' ? 'badge-info' : 'badge-danger')) ?>">
                                <?= esc($v['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="/verifications/view/<?= $v['id'] ?>" class="btn btn-sm btn-secondary">Audit Details</a>
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
