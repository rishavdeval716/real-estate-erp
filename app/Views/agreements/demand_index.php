<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Rent Demands</span>
        </div>
        <h1 class="page-title">Rent Demands & Billing</h1>
        <p class="page-subtitle">Generate monthly rental invoices, assess automatic late fees, and track unpaid balances.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openGenerateModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Generate Monthly Rent Demand
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Demanded Value</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);">₹<?= number_format((float)$kpi['total_demanded'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Collected</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$kpi['total_collected'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Outstanding Due Balance</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);">₹<?= number_format((float)$kpi['total_balance'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Overdue Demands</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['overdue_count']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/rent-demands" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Demand #, tenant name, lease agreement..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Billing Month</label>
                <input type="month" name="billing_period" class="form-control" value="<?= esc($filters['billing_period']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="unpaid" <?= $filters['status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="partially_paid" <?= $filters['status'] === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                    <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Fully Paid</option>
                    <option value="overdue" <?= $filters['status'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/rent-demands" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Rent Demands Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Demand Notice #</th>
                        <th>Billing Period</th>
                        <th>Tenant</th>
                        <th>Property & Unit</th>
                        <th>Due Date</th>
                        <th>Total Amount</th>
                        <th>Late Fee</th>
                        <th>Paid</th>
                        <th>Outstanding Due</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demands)): ?>
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No rent demands found matching criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($demands as $d): ?>
                    <tr>
                        <td>
                            <a href="/rent-demands/view/<?= $d['id'] ?>" style="font-weight: 600; color: var(--primary-600);">
                                <?= esc($d['demand_number']) ?>
                            </a>
                            <div style="font-size: 0.75rem; color: var(--slate-400);">Lease: <?= esc($d['agreement_number']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="font-size: 0.8rem; font-weight: 600;">
                                <?= date('F Y', strtotime($d['billing_period'] . '-01')) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($d['tenant_name']) ?></div>
                            <span class="badge badge-light" style="font-size: 0.7rem;"><?= esc($d['tenant_code']) ?></span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($d['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($d['unit_number'] ?? 'All') ?></div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;"><?= date('d M Y', strtotime($d['due_date'])) ?></div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--slate-800);">₹<?= number_format((float)$d['total_amount'], 2) ?></span>
                        </td>
                        <td>
                            <?php if ((float)$d['late_fee'] > 0): ?>
                            <span style="color: var(--rose-600); font-weight: 600;">+₹<?= number_format((float)$d['late_fee'], 2) ?></span>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="color: var(--emerald-600); font-weight: 600;">₹<?= number_format((float)$d['paid_amount'], 2) ?></span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: <?= (float)$d['balance_amount'] > 0 ? 'var(--rose-600)' : 'var(--slate-700)' ?>;">
                                ₹<?= number_format((float)$d['balance_amount'], 2) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $badgeClass = match($d['status']) {
                                'paid'           => 'badge-success',
                                'partially_paid' => 'badge-info',
                                'overdue'        => 'badge-danger',
                                default          => 'badge-warning',
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($d['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="/rent-demands/view/<?= $d['id'] ?>" class="btn btn-sm btn-secondary" title="View Demand & Collections">View</a>
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

<!-- Modal: Generate Rent Demand -->
<div id="generateModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Generate Monthly Rent Demand</h3>
            <button type="button" onclick="closeGenerateModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/rent-demands/generate">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Active Lease Contract <span style="color: var(--rose-500);">*</span></label>
                    <select name="lease_id" class="form-control" required>
                        <option value="">-- Select Active Lease --</option>
                        <?php foreach ($activeLeases as $al): ?>
                        <option value="<?= $al['id'] ?>">
                            <?= esc($al['agreement_number']) ?> &mdash; <?= esc($al['tenant_name']) ?> (<?= esc($al['property_title']) ?>) &bull; ₹<?= number_format((float)$al['monthly_rent'], 2) ?>/mo
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Billing Period <span style="color: var(--rose-500);">*</span></label>
                        <input type="month" name="billing_period" class="form-control" value="<?= date('Y-m') ?>" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Payment Due Date <span style="color: var(--rose-500);">*</span></label>
                        <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-05', strtotime('+1 month')) ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Custom Maintenance (₹)</label>
                        <input type="number" step="0.01" name="maintenance" class="form-control" placeholder="Defaults to lease terms">
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Other Utilities / Surcharges (₹)</label>
                        <input type="number" step="0.01" name="other_charges" class="form-control" placeholder="0.00" value="0.00">
                    </div>
                </div>

                <div style="background: var(--slate-50); padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.8rem; color: var(--slate-600);">
                    &bull; Standard base rent will be fetched dynamically from the active contract.<br>
                    &bull; Commercial leases automatically compute 18% GST on total charges.<br>
                    &bull; Prevents duplicate generation for the same lease and billing month.
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeGenerateModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Notice</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGenerateModal() {
    document.getElementById('generateModal').style.display = 'flex';
}
function closeGenerateModal() {
    document.getElementById('generateModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
