<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/expenses">Property Expenses</a> &rsaquo;
            <span><?= esc($expense['expense_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($expense['title']) ?></h1>
        <p class="page-subtitle">Voucher: <?= esc($expense['expense_code']) ?> &bull; Payee: <?= esc($expense['payee_vendor']) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/expenses/edit/<?= $expense['id'] ?>" class="btn btn-secondary">Edit Expense</a>
        <a href="/expenses" class="btn btn-secondary">Back to Expenses</a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Expense Payment Voucher</h3>
        <span class="badge <?= $expense['status'] === 'Paid' ? 'badge-success' : 'badge-info' ?>" style="font-size: 0.9rem; padding: 0.35rem 0.75rem;">
            <?= esc($expense['status']) ?>
        </span>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <table class="table" style="font-size: 0.9rem; margin-bottom: 1.5rem;">
            <tr>
                <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Voucher Number</td>
                <td><strong style="color: var(--primary); font-size: 1.05rem;"><?= esc($expense['expense_code']) ?></strong></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Expense Category</td>
                <td><span class="badge badge-info"><?= esc($expense['category_name']) ?></span></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Associated Property</td>
                <td><?= esc($expense['property_title'] ?: 'Corporate General') ?></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Associated Project</td>
                <td><?= esc($expense['project_name'] ?: 'None') ?></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Payee Vendor</td>
                <td><strong><?= esc($expense['payee_vendor']) ?></strong></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Disbursement Date</td>
                <td><?= date('d F Y', strtotime($expense['expense_date'])) ?></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Payment Method</td>
                <td><?= esc($expense['payment_method']) ?></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Net Subtotal Amount</td>
                <td>₹<?= number_format((float)$expense['amount'], 2) ?></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Taxes / GST Assessed</td>
                <td>₹<?= number_format((float)$expense['tax_amount'], 2) ?></td>
            </tr>
            <tr style="background: var(--slate-50); font-size: 1.1rem;">
                <td style="font-weight: 800; color: var(--slate-900);">Total Disbursement</td>
                <td><strong style="color: var(--danger);">₹<?= number_format((float)$expense['total_amount'], 2) ?></strong></td>
            </tr>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Remarks / Ledger Notes</td>
                <td><?= esc($expense['notes'] ?: 'No additional notes provided.') ?></td>
            </tr>
            <?php if (!empty($expense['receipt_file'])): ?>
            <tr>
                <td style="color: var(--slate-500); font-weight: 600;">Attached Receipt</td>
                <td>
                    <a href="/<?= esc($expense['receipt_file']) ?>" class="btn btn-sm btn-secondary" target="_blank">Download Receipt</a>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
