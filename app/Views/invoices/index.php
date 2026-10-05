<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/invoices">Sales</a> &rsaquo;
            <span>Invoices</span>
        </div>
        <h1 class="page-title">Invoice Management</h1>
        <p class="page-subtitle">Generate, issue, and manage milestone tax invoices for buyer bookings.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/invoices/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Generate Invoice
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/invoices" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Invoice #, booking #, customer..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Draft" <?= $filters['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Issued" <?= $filters['status'] === 'Issued' ? 'selected' : '' ?>>Issued</option>
                    <option value="Partially Paid" <?= $filters['status'] === 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="Paid" <?= $filters['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Overdue" <?= $filters['status'] === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                    <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/invoices" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No invoices found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td>
                                    <a href="/invoices/view/<?= $inv['id'] ?>" style="font-weight: 700; color: var(--brand-primary); text-decoration: none; font-family: monospace;">
                                        <?= esc($inv['invoice_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="/bookings/view/<?= $inv['booking_id'] ?>" style="font-family: monospace; color: var(--slate-700);">
                                        <?= esc($inv['booking_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($inv['first_name'] . ' ' . $inv['last_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($inv['customer_phone']) ?></div>
                                </td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($inv['due_date'])) ?></td>
                                <td style="font-weight: 700;">₹<?= number_format($inv['total_amount'], 2) ?></td>
                                <td style="font-weight: 700; color: #dc2626;">₹<?= number_format($inv['balance_amount'], 2) ?></td>
                                <td>
                                    <?php
                                    $stClass = 'badge-secondary';
                                    if ($inv['status'] === 'Paid') $stClass = 'badge-success';
                                    elseif ($inv['status'] === 'Cancelled') $stClass = 'badge-danger';
                                    elseif ($inv['status'] === 'Issued' || $inv['status'] === 'Partially Paid') $stClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $stClass ?>"><?= esc($inv['status']) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/invoices/view/<?= $inv['id'] ?>" class="btn btn-sm btn-secondary">View / Print</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pager)): ?>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-200);">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
