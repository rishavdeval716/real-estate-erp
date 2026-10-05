<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/expenses">Property Expenses</a> &rsaquo;
            <span>Record Expense</span>
        </div>
        <h1 class="page-title">Record Property Expense</h1>
        <p class="page-subtitle">Log building maintenance, repairs, utility payments, municipal taxes, and vendor disbursements.</p>
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

        <form method="POST" action="/expenses/store" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Expense Title / Description <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Annual Elevator Comprehensive AMC Service" value="<?= old('title') ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expense Category <span style="color: var(--danger);">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">— Select Category —</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expense Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="expense_date" class="form-control" required value="<?= old('expense_date', date('Y-m-d')) ?>">
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
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?> (<?= esc($p['property_code']) ?>)</option>
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
                            <option value="<?= $proj['id'] ?>"><?= esc($proj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Payee Vendor / Service Provider <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="payee_vendor" class="form-control" required placeholder="e.g. Otis Elevators Ltd" value="<?= old('payee_vendor') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Payment Method <span style="color: var(--danger);">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="RTGS">RTGS</option>
                            <option value="NEFT">NEFT</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Cash">Cash</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Net Subtotal (₹) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" name="amount" id="exp_amount" class="form-control" required value="<?= old('amount') ?>" oninput="calcExpTotal()">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Tax / GST (₹)</label>
                        <input type="number" step="0.01" name="tax_amount" id="exp_tax" class="form-control" value="<?= old('tax_amount', '0.00') ?>" oninput="calcExpTotal()">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Total Outlay (₹)</label>
                        <input type="text" id="exp_total" class="form-control" readonly style="font-weight: 700; background: var(--slate-100);">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="status" class="form-control">
                            <option value="Paid">Paid</option>
                            <option value="Approved">Approved (Awaiting Payout)</option>
                            <option value="Pending Approval">Pending Approval</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Upload Receipt / Invoice Slip</label>
                        <input type="file" name="receipt_file" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes & Ledger Reference</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Challan / Bill numbers, breakdown notes..."><?= old('notes') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/expenses" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Record Expense Voucher</button>
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
calcExpTotal();
</script>

<?= $this->endSection() ?>
