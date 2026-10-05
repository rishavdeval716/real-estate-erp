<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/expenses">Property Expenses</a> &rsaquo;
            <span>Edit Expense</span>
        </div>
        <h1 class="page-title">Edit Expense: <?= esc($expense['expense_code']) ?></h1>
        <p class="page-subtitle">Update payee vendor, amount breakdown, and approval status.</p>
    </div>
</div>

<div class="card" style="max-width: 850px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Expense Voucher Details</h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="/expenses/update/<?= $expense['id'] ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Expense Title / Description <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" required value="<?= old('title', $expense['title']) ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expense Category <span style="color: var(--danger);">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $expense['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expense Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="expense_date" class="form-control" required value="<?= old('expense_date', $expense['expense_date']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Property (Optional)</label>
                        <select name="property_id" class="form-control">
                            <option value="">— Corporate General / Not Property Specific —</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $expense['property_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Project (Optional)</label>
                        <select name="project_id" class="form-control">
                            <option value="">— Not Project Specific —</option>
                            <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>" <?= $expense['project_id'] == $proj['id'] ? 'selected' : '' ?>><?= esc($proj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Payee Vendor / Service Provider <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="payee_vendor" class="form-control" required value="<?= old('payee_vendor', $expense['payee_vendor']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Payment Method <span style="color: var(--danger);">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <?php foreach (['Bank Transfer', 'RTGS', 'NEFT', 'UPI', 'Cheque', 'Credit Card', 'Cash', 'Other'] as $pm): ?>
                            <option value="<?= $pm ?>" <?= $expense['payment_method'] === $pm ? 'selected' : '' ?>><?= $pm ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Net Subtotal (₹) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" name="amount" id="exp_amount" class="form-control" required value="<?= old('amount', $expense['amount']) ?>" oninput="calcExpTotal()">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Tax / GST (₹)</label>
                        <input type="number" step="0.01" name="tax_amount" id="exp_tax" class="form-control" value="<?= old('tax_amount', $expense['tax_amount']) ?>" oninput="calcExpTotal()">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Total Outlay (₹)</label>
                        <input type="text" id="exp_total" class="form-control" readonly style="font-weight: 700; background: var(--slate-100);" value="₹ <?= number_format((float)$expense['total_amount'], 2) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Status</label>
                <select name="status" class="form-control">
                    <option value="Paid" <?= $expense['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Approved" <?= $expense['status'] === 'Approved' ? 'selected' : '' ?>>Approved (Awaiting Payout)</option>
                    <option value="Pending Approval" <?= $expense['status'] === 'Pending Approval' ? 'selected' : '' ?>>Pending Approval</option>
                    <option value="Draft" <?= $expense['status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Rejected" <?= $expense['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Notes & Ledger Reference</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes', $expense['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/expenses/view/<?= $expense['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Expense Voucher</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcExpTotal() {
    var amt = parseFloat(document.getElementById('exp_amount').value) || 0;
    var tax = parseFloat(document.getElementById('exp_tax').value) || 0;
    document.getElementById('exp_total').value = '₹ ' + (amt + tax).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>

<?= $this->endSection() ?>
