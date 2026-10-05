<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/payments">Sales</a> &rsaquo;
            <span>Payments</span>
        </div>
        <h1 class="page-title">Payment Management</h1>
        <p class="page-subtitle">Track incoming installment collections, milestone allocations, and payment receipts.</p>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/payments" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Payment #, booking #, customer, reference..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Payment Method</label>
                <select name="payment_method" class="form-control">
                    <option value="">All Methods</option>
                    <option value="Bank Transfer" <?= $filters['payment_method'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    <option value="UPI" <?= $filters['payment_method'] === 'UPI' ? 'selected' : '' ?>>UPI</option>
                    <option value="NEFT" <?= $filters['payment_method'] === 'NEFT' ? 'selected' : '' ?>>NEFT</option>
                    <option value="RTGS" <?= $filters['payment_method'] === 'RTGS' ? 'selected' : '' ?>>RTGS</option>
                    <option value="Cheque" <?= $filters['payment_method'] === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                    <option value="Cash" <?= $filters['payment_method'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="Online Gateway" <?= $filters['payment_method'] === 'Online Gateway' ? 'selected' : '' ?>>Online Gateway</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Received" <?= $filters['status'] === 'Received' ? 'selected' : '' ?>>Received</option>
                    <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="Failed" <?= $filters['status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/payments" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Milestone Target</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Receipt</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No payment records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--brand-primary);">
                                    <?= esc($p['payment_number']) ?>
                                </td>
                                <td>
                                    <a href="/bookings/view/<?= $p['booking_id'] ?>" style="font-family: monospace; color: var(--slate-700);">
                                        <?= esc($p['booking_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($p['customer_phone']) ?></div>
                                </td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                <td style="font-size: 0.85rem;"><?= esc($p['milestone_name'] ?: 'Direct Allocation') ?></td>
                                <td style="font-weight: 700; color: var(--slate-900);">₹<?= number_format($p['amount'], 2) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($p['payment_method']) ?></span></td>
                                <td>
                                    <span class="badge <?= $p['status'] === 'Received' ? 'badge-success' : ($p['status'] === 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($p['receipt_number'])): ?>
                                        <a href="/receipts" class="btn btn-sm btn-secondary" title="Official Receipt">
                                            <?= esc($p['receipt_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.75rem; color: var(--slate-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($p['status'] !== 'Cancelled'): ?>
                                        <form method="POST" action="/payments/cancel/<?= $p['id'] ?>" onsubmit="return confirm('Cancel this payment record? This will reverse milestone allocation.');" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>
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
