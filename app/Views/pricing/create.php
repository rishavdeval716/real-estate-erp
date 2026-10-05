<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/pricing">Pricing</a>
            <span class="separator">/</span>
            <span>New Valuation</span>
        </div>
        <h1 class="page-title">Record Pricing & Valuation</h1>
        <p class="page-subtitle">Schedule a base price, per sq.ft rate, market valuation, and seasonal discount</p>
    </div>
    <div>
        <a href="/pricing" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Pricing
        </a>
    </div>
</div>

<div class="card" style="max-width: 860px;">
    <div class="card-header">
        <h2 class="card-title">Valuation Parameters</h2>
    </div>
    <div class="card-body">
        <form action="/pricing/store" method="POST">
            <?= csrf_field() ?>

            <!-- Property & Unit selection -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="property_id" class="form-label required">Target Property</label>
                        <select name="property_id" id="property_id" class="form-control" onchange="window.location.href='/pricing/create?property_id='+this.value" required>
                            <option value="">Select Property</option>
                            <?php foreach ($properties as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= ($propertyId == $p['id'] || old('property_id') == $p['id']) ? 'selected' : '' ?>>
                                    <?= esc($p['title']) ?> (<?= esc($p['property_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="unit_id" class="form-label">Specific Unit (Optional)</label>
                        <select name="unit_id" id="unit_id" class="form-control">
                            <option value="">Whole Property Valuation</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= esc($u['id']) ?>" <?= old('unit_id', $unitId) == $u['id'] ? 'selected' : '' ?>>
                                    Unit <?= esc($u['unit_number']) ?> (<?= esc($u['flat_type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Base Price & Per SqFt -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="base_price" class="form-label required">Base Listing Price (₹)</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="<?= esc(old('base_price', $selectedProperty['price'] ?? '')) ?>" placeholder="e.g. 45000000" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="price_per_sqft" class="form-label">Price per Sq.Ft (₹)</label>
                        <input type="number" step="0.01" name="price_per_sqft" id="price_per_sqft" class="form-control" value="<?= esc(old('price_per_sqft')) ?>" placeholder="Leave blank to auto-calculate from area">
                    </div>
                </div>
            </div>

            <!-- Market Price & Negotiated Price -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="market_price" class="form-label">Market Valuation (₹)</label>
                        <input type="number" step="0.01" name="market_price" id="market_price" class="form-control" value="<?= esc(old('market_price')) ?>" placeholder="e.g. 48000000">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="negotiated_price" class="form-label">Negotiated Target Deal Price (₹)</label>
                        <input type="number" step="0.01" name="negotiated_price" id="negotiated_price" class="form-control" value="<?= esc(old('negotiated_price')) ?>" placeholder="e.g. 44000000">
                    </div>
                </div>
            </div>

            <!-- Discount & Effective Dates -->
            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label for="discount" class="form-label">Rebate / Discount (₹)</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="<?= esc(old('discount', '0.00')) ?>">
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="effective_from" class="form-label">Effective From</label>
                        <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= esc(old('effective_from', date('Y-m-d'))) ?>">
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="effective_to" class="form-label">Effective To</label>
                        <input type="date" name="effective_to" id="effective_to" class="form-control" value="<?= esc(old('effective_to')) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="remarks" class="form-label">Remarks / Valuation Note</label>
                <textarea name="remarks" id="remarks" rows="2" class="form-control" placeholder="Specify basis of valuation, festive scheme, or market review..."><?= esc(old('remarks')) ?></textarea>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                    <input type="checkbox" name="update_primary_price" value="1" checked>
                    <span>Update current active property/unit price with this base price</span>
                </label>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Valuation Schedule</button>
                <a href="/pricing" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
