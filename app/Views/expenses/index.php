<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Property Expenses</span>
        </div>
        <h1 class="page-title">Property Expense Management</h1>
        <p class="page-subtitle">Track building maintenance, repairs, statutory municipal taxes, utility bills, and vendor disbursements.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/expenses/categories" class="btn btn-secondary">Expense Categories</a>
        <a href="/expenses/report" class="btn btn-secondary">Annual Statement</a>
        <a href="/expenses/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Record Expense
        </a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Outlay Filtered</div>
            <div class="metric-value" style="color: var(--danger);">₹<?= number_format((float)($summary['total_spend'] ?? 0), 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);"><?= number_format((int)($summary['total_count'] ?? 0)) ?> Recorded Vouchers</div>
        </div>
        <div class="metric-icon-box" style="background: var(--danger-light); color: var(--danger);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Net Subtotal</div>
            <div class="metric-value">₹<?= number_format((float)($summary['subtotal'] ?? 0), 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Pre-tax operating base</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Input GST / Taxes</div>
            <div class="metric-value" style="color: var(--info);">₹<?= number_format((float)($summary['total_tax'] ?? 0), 2) ?></div>
            <div class="metric-meta" style="color: var(--info);">Recoverable input credits</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/expenses" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 180px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Keyword</label>
                <input type="text" name="keyword" class="form-control" placeholder="Search title, payee, voucher #..." value="<?= esc($filters['keyword']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $filters['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Property</label>
                <select name="property_id" class="form-control">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filters['property_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from']) ?>">
            </div>
            <div style="flex: 1; min-width: 120px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to']) ?>">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/expenses" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Voucher #</th>
                        <th>Expense Title</th>
                        <th>Category</th>
                        <th>Property / Project</th>
                        <th>Payee Vendor</th>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No expense records found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($expenses as $e): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($e['expense_code']) ?></strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($e['title']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= esc($e['category_name']) ?></span>
                        </td>
                        <td>
                            <?= esc($e['property_title'] ?: ($e['project_name'] ?: 'Corporate General')) ?>
                        </td>
                        <td><?= esc($e['payee_vendor']) ?></td>
                        <td><?= date('d M Y', strtotime($e['expense_date'])) ?></td>
                        <td><?= esc($e['payment_method']) ?></td>
                        <td>
                            <strong style="color: var(--danger);">₹<?= number_format((float)$e['total_amount'], 2) ?></strong>
                            <?php if ($e['tax_amount'] > 0): ?>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Tax: ₹<?= number_format((float)$e['tax_amount'], 2) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $e['status'] === 'Paid' ? 'badge-success' : ($e['status'] === 'Approved' ? 'badge-info' : 'badge-warning') ?>">
                                <?= esc($e['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.4rem;">
                                <a href="/expenses/view/<?= $e['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                                <a href="/expenses/edit/<?= $e['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
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
