<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/invoices">Invoices</a> &rsaquo;
            <span>Generate Invoice</span>
        </div>
        <h1 class="page-title">Generate Tax Invoice</h1>
        <p class="page-subtitle">Issue an official payment milestone demand or final settlement tax invoice.</p>
    </div>
    <div>
        <a href="/invoices" class="btn btn-secondary">Back to Invoices</a>
    </div>
</div>

<div class="card" style="max-width: 850px; margin: 0 auto;">
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/invoices/store">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Select Associated Booking <span style="color: red;">*</span></label>
                <select name="booking_id" class="form-control" required id="bookingSelect">
                    <option value="">-- Select Active Booking --</option>
                    <?php foreach ($bookings as $b): ?>
                        <option value="<?= $b['id'] ?>" 
                                data-amount="<?= $b['final_amount'] ?>"
                                <?= old('booking_id', $bookingId) == $b['id'] ? 'selected' : '' ?>>
                            <?= esc($b['booking_number']) ?> &bull; <?= esc($b['first_name'] . ' ' . $b['last_name']) ?> &bull; Unit <?= esc($b['unit_number']) ?> (₹<?= number_format($b['final_amount'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Invoice Date <span style="color: red;">*</span></label>
                    <input type="date" name="invoice_date" class="form-control" required value="<?= old('invoice_date', date('Y-m-d')) ?>">
                </div>
                <div>
                    <label class="form-label">Payment Due Date <span style="color: red;">*</span></label>
                    <input type="date" name="due_date" class="form-control" required value="<?= old('due_date', date('Y-m-d', strtotime('+15 days'))) ?>">
                </div>
                <div>
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Issued">Issued</option>
                        <option value="Draft">Draft</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Subtotal Demand (₹) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" required value="<?= old('subtotal', $selectedBooking['final_amount'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Discount / Waiver (₹)</label>
                    <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="<?= old('discount', '0') ?>">
                </div>
                <div>
                    <label class="form-label">Applicable Tax / GST (₹)</label>
                    <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="<?= old('tax', '0') ?>">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1rem;">
                <a href="/invoices" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Generate & Issue Invoice</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('bookingSelect');
    const subtotal = document.getElementById('subtotal');

    bookingSelect.addEventListener('change', function() {
        const sel = bookingSelect.options[bookingSelect.selectedIndex];
        if (sel && sel.dataset.amount) {
            subtotal.value = parseFloat(sel.dataset.amount).toFixed(2);
        }
    });
});
</script>

<?= $this->endSection() ?>
