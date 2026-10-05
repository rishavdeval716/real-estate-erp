<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>CAM Charges</span>
        </div>
        <h1 class="page-title">Common Area Maintenance (CAM) Billing</h1>
        <p class="page-subtitle">Configure per-square-foot or flat-rate maintenance distribution, 18% GST tax, and collection status.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openCamModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Generate CAM Charge
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total CAM Assessments</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total_count']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Billed Amount</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);">₹<?= number_format((float)$kpi['total_billed'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Collected Amount</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$kpi['total_paid'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Unbilled / Pending</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['unbilled']) ?></div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/cam-charges" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Target Property</label>
                <select name="property_id" class="form-control">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $filters['property_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="unbilled" <?= $filters['status'] === 'unbilled' ? 'selected' : '' ?>>Unbilled</option>
                    <option value="billed" <?= $filters['status'] === 'billed' ? 'selected' : '' ?>>Billed</option>
                    <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/cam-charges" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- CAM Charges Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>CAM Code</th>
                        <th>Property & Unit</th>
                        <th>Tenant / Occupant</th>
                        <th>Billing Model</th>
                        <th>Area (Sq.Ft)</th>
                        <th>Rate (₹)</th>
                        <th>Period</th>
                        <th>Tax (18% GST)</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($charges)): ?>
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No CAM charge records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($charges as $c): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($c['cam_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($c['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($c['unit_number'] ?? 'Common Facility') ?></div>
                        </td>
                        <td>
                            <?= esc($c['tenant_name'] ?? 'Facility Common') ?>
                        </td>
                        <td>
                            <span class="badge badge-light" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($c['billing_model'])) ?>
                            </span>
                        </td>
                        <td><?= number_format((float)$c['area_sqft'], 2) ?> sq.ft</td>
                        <td>₹<?= number_format((float)$c['rate'], 2) ?></td>
                        <td>
                            <span style="font-weight: 500;"><?= esc($c['period']) ?></span>
                        </td>
                        <td>₹<?= number_format((float)$c['tax'], 2) ?></td>
                        <td>
                            <strong style="color: var(--slate-900);">₹<?= number_format((float)$c['total'], 2) ?></strong>
                        </td>
                        <td>
                            <?php
                            $sBadge = match($c['status']) {
                                'paid'     => 'badge-success',
                                'billed'   => 'badge-info',
                                default    => 'badge-warning',
                            };
                            ?>
                            <span class="badge <?= $sBadge ?>" style="text-transform: capitalize;">
                                <?= esc($c['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <form method="POST" action="/cam-charges/status/<?= $c['id'] ?>" style="display: inline-flex; gap: 0.25rem;">
                                <?= csrf_field() ?>
                                <select name="status" class="form-control form-control-sm" style="width: auto; padding: 0.2rem 0.5rem; font-size: 0.75rem;" onchange="this.form.submit()">
                                    <option value="unbilled" <?= $c['status'] === 'unbilled' ? 'selected' : '' ?>>Unbilled</option>
                                    <option value="billed" <?= $c['status'] === 'billed' ? 'selected' : '' ?>>Billed</option>
                                    <option value="paid" <?= $c['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
    <div class="card-footer" style="padding: 1rem;">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal: Generate CAM Charge -->
<div id="camModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Generate CAM Charge Distribution</h3>
            <button type="button" onclick="closeCamModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/cam-charges/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Target Property <span style="color: var(--rose-500);">*</span></label>
                    <select name="property_id" class="form-control" required>
                        <option value="">-- Choose Property --</option>
                        <?php foreach ($properties as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Unit (Optional)</label>
                        <select name="property_unit_id" class="form-control">
                            <option value="">-- Entire Property / Common --</option>
                            <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>">Unit <?= esc($u['unit_number']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Tenant (Optional)</label>
                        <select name="tenant_id" class="form-control">
                            <option value="">-- Unassigned --</option>
                            <?php foreach ($tenants as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= esc($t['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Billing Model <span style="color: var(--rose-500);">*</span></label>
                        <select name="billing_model" class="form-control" required>
                            <option value="per_sqft">Per Sq.Ft Calculation</option>
                            <option value="flat_rate">Flat Rate Charge</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Area (Sq.Ft) <span style="color: var(--rose-500);">*</span></label>
                        <input type="number" step="0.01" name="area_sqft" class="form-control" placeholder="e.g. 1200" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Rate (₹/sqft or Flat) <span style="color: var(--rose-500);">*</span></label>
                        <input type="number" step="0.01" name="rate" class="form-control" placeholder="e.g. 4.50" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Billing Period <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="period" class="form-control" placeholder="e.g. Oct 2026 or Q4-2026" required>
                    </div>
                </div>

                <div style="background: var(--slate-50); padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.8rem; color: var(--slate-600);">
                    &bull; System computes `Subtotal = Area &times; Rate` (or Flat Rate).<br>
                    &bull; Statutory 18% GST is automatically calculated and added to the billing total.
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeCamModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate CAM Invoice</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCamModal() {
    document.getElementById('camModal').style.display = 'flex';
}
function closeCamModal() {
    document.getElementById('camModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
