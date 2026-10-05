<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/agreements">Agreements</a> &rsaquo;
            <span>Generate Agreement</span>
        </div>
        <h1 class="page-title">Generate Sales Agreement</h1>
        <p class="page-subtitle">Draft a legally-binding property agreement with configurable contractual terms and conditions.</p>
    </div>
    <div>
        <a href="/agreements" class="btn btn-secondary">Back to Agreements</a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
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

        <form method="POST" action="/agreements/store">
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
                    <label class="form-label">Agreement Type <span style="color: red;">*</span></label>
                    <select name="agreement_type" class="form-control" required>
                        <option value="Agreement to Sale">Agreement to Sale</option>
                        <option value="Sale Deed">Sale Deed</option>
                        <option value="Allotment Letter">Allotment Letter</option>
                        <option value="Tripartite Agreement">Tripartite Agreement</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Agreement Date <span style="color: red;">*</span></label>
                    <input type="date" name="agreement_date" class="form-control" required value="<?= old('agreement_date', date('Y-m-d')) ?>">
                </div>
                <div>
                    <label class="form-label">Total Agreed Consideration (₹) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="total_value" id="totalValue" class="form-control" required value="<?= old('total_value', $selectedBooking['final_amount'] ?? '') ?>">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Contractual Terms & Conditions <span style="color: red;">*</span></label>
                <textarea name="terms_conditions" class="form-control" rows="8" required style="font-family: inherit; font-size: 0.85rem; line-height: 1.6;"><?= old('terms_conditions', $defaultTerms) ?></textarea>
                <small style="color: var(--slate-500); display: block; margin-top: 0.25rem;">
                    Configurable clauses: Property specifications, payment schedule adherence, possession handover terms, and default policies.
                </small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Special Conditions / Customized Clauses</label>
                <textarea name="special_conditions" class="form-control" rows="3" placeholder="Add custom terms agreed with buyer (e.g. customized interior fittings, parking bay assignment, extended grace period)..."><?= old('special_conditions') ?></textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control" placeholder="Internal notes..." value="<?= old('remarks') ?>">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1rem;">
                <a href="/agreements" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save & Generate Agreement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('bookingSelect');
    const totalValue = document.getElementById('totalValue');

    bookingSelect.addEventListener('change', function() {
        const sel = bookingSelect.options[bookingSelect.selectedIndex];
        if (sel && sel.dataset.amount) {
            totalValue.value = parseFloat(sel.dataset.amount).toFixed(2);
        }
    });
});
</script>

<?= $this->endSection() ?>
