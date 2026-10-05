<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/receipts">Sales</a> &rsaquo;
            <span>Receipts</span>
        </div>
        <h1 class="page-title">Payment Receipts</h1>
        <p class="page-subtitle">Official acknowledgements and printable receipts for received buyer payments.</p>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/receipts" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Receipt #, payment #, customer, reference..." value="<?= esc($filters['search']) ?>">
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
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/receipts" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Receipts Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Payment #</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receipts)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No receipt records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($receipts as $r): ?>
                            <tr>
                                <td>
                                    <a href="/receipts/view/<?= $r['id'] ?>" style="font-weight: 700; color: var(--brand-primary); text-decoration: none; font-family: monospace;">
                                        <?= esc($r['receipt_number']) ?>
                                    </a>
                                </td>
                                <td style="font-family: monospace; color: var(--slate-600);"><?= esc($r['payment_number'] ?? '-') ?></td>
                                <td>
                                    <a href="/bookings/view/<?= $r['booking_id'] ?>" style="font-family: monospace; color: var(--slate-700);">
                                        <?= esc($r['booking_number'] ?? '-') ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($r['customer_phone'] ?? '') ?></div>
                                </td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($r['receipt_date'])) ?></td>
                                <td style="font-weight: 700; color: #16a34a;">₹<?= number_format($r['amount'], 2) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($r['payment_method']) ?></span></td>
                                <td style="font-size: 0.8rem; font-family: monospace; color: var(--slate-600);"><?= esc($r['transaction_reference'] ?: '-') ?></td>
                                <td style="text-align: right;">
                                    <a href="/receipts/view/<?= $r['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print Receipt</a>
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
