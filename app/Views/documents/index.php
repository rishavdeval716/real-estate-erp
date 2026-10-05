<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Legal & Documents</span>
        </div>
        <h1 class="page-title">Legal & Property Document Management</h1>
        <p class="page-subtitle">Centralized repository for title deeds, municipal sanction plans, fire NOCs, RERA filings, and compliance audit certificates.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/documents/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload Document
        </a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Documents</div>
            <div class="metric-value"><?= number_format($totalDocs) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Archived compliance files</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Verified Documents</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($verifiedDocs) ?></div>
            <div class="metric-meta" style="color: var(--success);">Cleared legal compliance</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Pending Review</div>
            <div class="metric-value" style="color: var(--warning);"><?= number_format($pendingDocs) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Awaiting legal panel review</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/documents" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search title, document code, filename..." value="<?= esc($search) ?>">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Document Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="Ownership / Title Deed" <?= $category === 'Ownership / Title Deed' ? 'selected' : '' ?>>Ownership / Title Deed</option>
                    <option value="Building Approval Plan" <?= $category === 'Building Approval Plan' ? 'selected' : '' ?>>Building Approval Plan</option>
                    <option value="NOC Clearance" <?= $category === 'NOC Clearance' ? 'selected' : '' ?>>NOC Clearance</option>
                    <option value="Occupancy Certificate" <?= $category === 'Occupancy Certificate' ? 'selected' : '' ?>>Occupancy Certificate</option>
                    <option value="RERA Certificate" <?= $category === 'RERA Certificate' ? 'selected' : '' ?>>RERA Certificate</option>
                    <option value="Encumbrance Certificate" <?= $category === 'Encumbrance Certificate' ? 'selected' : '' ?>>Encumbrance Certificate</option>
                    <option value="Property Tax Receipt" <?= $category === 'Property Tax Receipt' ? 'selected' : '' ?>>Property Tax Receipt</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Verified" <?= $status === 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Under Review" <?= $status === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="Rejected" <?= $status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="Expired" <?= $status === 'Expired' ? 'selected' : '' ?>>Expired</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/documents" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Documents Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Doc Code</th>
                        <th>Document Title</th>
                        <th>Category</th>
                        <th>Associated Property</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No legal or compliance documents found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($documents as $d): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($d['document_code']) ?></strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($d['title']) ?></div>
                            <div style="font-size: 0.78rem; color: var(--slate-500);"><?= esc($d['file_name']) ?> &bull; <?= number_format($d['file_size'] / 1024, 0) ?> KB</div>
                        </td>
                        <td><span class="badge badge-info"><?= esc($d['document_category']) ?></span></td>
                        <td><?= esc($d['property_title'] ?: ($d['project_name'] ?: 'Corporate Master')) ?></td>
                        <td><?= $d['issue_date'] ? date('d M Y', strtotime($d['issue_date'])) : '—' ?></td>
                        <td>
                            <?php if ($d['expiry_date']): ?>
                            <span style="<?= strtotime($d['expiry_date']) < time() ? 'color: var(--danger); font-weight: 700;' : '' ?>">
                                <?= date('d M Y', strtotime($d['expiry_date'])) ?>
                            </span>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">Perpetual</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $d['verification_status'] === 'Verified' ? 'badge-success' : ($d['verification_status'] === 'Pending' ? 'badge-warning' : 'badge-danger') ?>">
                                <?= esc($d['verification_status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.4rem;">
                                <a href="/documents/view/<?= $d['id'] ?>" class="btn btn-sm btn-secondary">Review</a>
                                <a href="/documents/edit/<?= $d['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
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
