<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/bookings">Bookings</a> &rsaquo;
            <span>Create Booking</span>
        </div>
        <h1 class="page-title">Create Property Booking</h1>
        <p class="page-subtitle">Allocate an available property unit to a registered buyer and initialize the construction payment schedule.</p>
    </div>
    <div>
        <a href="/bookings" class="btn btn-secondary">Back to Bookings</a>
    </div>
</div>

<div class="card" style="max-width: 950px; margin: 0 auto;">
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($selectedHoldId)): ?>
            <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                <strong>Unit Hold Conversion:</strong> This booking is converting active unit hold #<?= esc($selectedHoldId) ?> into a formal property booking.
            </div>
        <?php endif; ?>

        <form method="POST" action="/bookings/store" id="bookingForm">
            <?= csrf_field() ?>

            <!-- Customer & Executive -->
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                1. Customer & Sales Assignment
            </h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Buyer / Customer <span style="color: red;">*</span></label>
                    <select name="customer_id" class="form-control" required id="customerSelect">
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= old('customer_id', $selectedCustomerId) == $c['id'] ? 'selected' : '' ?>>
                                <?= esc($c['first_name'] . ' ' . $c['last_name']) ?> (<?= esc($c['customer_code']) ?> &bull; <?= esc($c['phone']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--slate-500); display: block; margin-top: 0.25rem;">
                        Customer not listed? <a href="/customers/create" target="_blank">Register new customer</a>
                    </small>
                </div>

                <div>
                    <label class="form-label">Sales Executive</label>
                    <select name="sales_executive_id" class="form-control">
                        <option value="">-- Assign Executive --</option>
                        <?php foreach ($executives as $ex): ?>
                            <option value="<?= $ex['id'] ?>" <?= old('sales_executive_id', session()->get('user_id')) == $ex['id'] ? 'selected' : '' ?>>
                                <?= esc($ex['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Property & Unit Selection -->
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                2. Property Unit Selection
            </h3>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Select Available / Reserved Unit <span style="color: red;">*</span></label>
                <select name="property_unit_id" class="form-control" required id="unitSelect">
                    <option value="">-- Select Property Unit --</option>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= $u['id'] ?>" 
                                data-price="<?= $u['unit_price'] ?>"
                                data-flat-type="<?= esc($u['flat_type']) ?>"
                                data-carpet="<?= esc($u['carpet_area']) ?>"
                                <?= old('property_unit_id', $selectedUnitId) == $u['id'] ? 'selected' : '' ?>>
                            <?= esc($u['project_name'] ?: $u['property_title']) ?> &bull; Unit <?= esc($u['unit_number']) ?> (<?= esc($u['flat_type']) ?>, <?= esc($u['carpet_area']) ?> sq.ft) - [Status: <?= esc($u['availability_status']) ?>] - ₹<?= number_format($u['unit_price'], 2) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Pricing Breakdown -->
            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                3. Financial Breakdown & Considerations
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Base Price (₹) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="base_price" id="basePrice" class="form-control" required value="<?= old('base_price', $selectedUnit['unit_price'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Discount / Concession (₹)</label>
                    <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="<?= old('discount', '0') ?>">
                </div>
                <div>
                    <label class="form-label">Tax / GST (₹)</label>
                    <input type="number" step="0.01" name="tax_amount" id="taxAmount" class="form-control" value="<?= old('tax_amount', '0') ?>">
                </div>
                <div>
                    <label class="form-label">Final Agreed Amount (₹)</label>
                    <input type="number" step="0.01" id="finalAmountDisplay" class="form-control" readonly style="font-weight: 700; background: var(--slate-100); color: var(--slate-900);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Token / EOI Amount (₹)</label>
                    <input type="number" step="0.01" name="token_amount" class="form-control" placeholder="0.00" value="<?= old('token_amount', '0') ?>">
                </div>
                <div>
                    <label class="form-label">Booking Advance Amount (₹)</label>
                    <input type="number" step="0.01" name="booking_amount" class="form-control" placeholder="0.00" value="<?= old('booking_amount', '0') ?>">
                </div>
                <div>
                    <label class="form-label">Booking Date <span style="color: red;">*</span></label>
                    <input type="date" name="booking_date" class="form-control" required value="<?= old('booking_date', date('Y-m-d')) ?>">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Remarks / Special Notes</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Payment terms, special discounts, custom fittings agreed..."><?= old('remarks') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1rem;">
                <a href="/bookings" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    Create Booking & Generate Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('unitSelect');
    const basePrice = document.getElementById('basePrice');
    const discount = document.getElementById('discount');
    const taxAmount = document.getElementById('taxAmount');
    const finalAmountDisplay = document.getElementById('finalAmountDisplay');

    function calculateFinal() {
        const bp = parseFloat(basePrice.value) || 0;
        const dc = parseFloat(discount.value) || 0;
        const tx = parseFloat(taxAmount.value) || 0;
        const finalVal = Math.max(0, bp - dc + tx);
        finalAmountDisplay.value = finalVal.toFixed(2);
    }

    unitSelect.addEventListener('change', function() {
        const selected = unitSelect.options[unitSelect.selectedIndex];
        if (selected && selected.dataset.price) {
            basePrice.value = parseFloat(selected.dataset.price).toFixed(2);
            calculateFinal();
        }
    });

    basePrice.addEventListener('input', calculateFinal);
    discount.addEventListener('input', calculateFinal);
    taxAmount.addEventListener('input', calculateFinal);

    calculateFinal();
});
</script>

<?= $this->endSection() ?>
