<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Security Deposits</span>
        </div>
        <h1 class="page-title">Security Deposits Register</h1>
        <p class="page-subtitle">Track tenant caution money, escrow deposits, deductions, and exit refund settlements.</p>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Deposits Count</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total_count']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Deposits Held</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-600);">₹<?= number_format((float)$kpi['total_held'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Refunded</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$kpi['total_refunded'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Adjusted / Deductions</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);">₹<?= number_format((float)$kpi['total_adjusted'], 2) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/deposits" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Deposit #, tenant, lease agreement..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Refund Status</label>
                <select name="refund_status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="held" <?= $filters['refund_status'] === 'held' ? 'selected' : '' ?>>Held in Escrow</option>
                    <option value="partially_refunded" <?= $filters['refund_status'] === 'partially_refunded' ? 'selected' : '' ?>>Partially Refunded</option>
                    <option value="refunded" <?= $filters['refund_status'] === 'refunded' ? 'selected' : '' ?>>Fully Refunded</option>
                    <option value="forfeited" <?= $filters['refund_status'] === 'forfeited' ? 'selected' : '' ?>>Forfeited</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/deposits" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Deposits Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Deposit Reference</th>
                        <th>Tenant</th>
                        <th>Lease Agreement</th>
                        <th>Property & Unit</th>
                        <th>Deposit Date</th>
                        <th>Original Amount</th>
                        <th>Refundable</th>
                        <th>Adjusted</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deposits)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No security deposit records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($deposits as $dep): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($dep['deposit_number']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($dep['tenant_name']) ?></div>
                            <span class="badge badge-light" style="font-size: 0.7rem;"><?= esc($dep['tenant_code']) ?></span>
                        </td>
                        <td>
                            <a href="/leases/view/<?= $dep['lease_id'] ?>" style="color: var(--slate-700); font-weight: 500;">
                                <?= esc($dep['agreement_number']) ?>
                            </a>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 500;"><?= esc($dep['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-400);">Unit: <?= esc($dep['unit_number'] ?? 'All') ?></div>
                        </td>
                        <td><?= date('d M Y', strtotime($dep['deposit_date'])) ?></td>
                        <td>
                            <span style="font-weight: 600; color: var(--slate-800);">₹<?= number_format((float)$dep['amount'], 2) ?></span>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--emerald-600);">₹<?= number_format((float)$dep['refundable_amount'], 2) ?></span>
                        </td>
                        <td>
                            <span style="color: var(--rose-600);">₹<?= number_format((float)$dep['adjusted_amount'], 2) ?></span>
                        </td>
                        <td>
                            <?php
                            $badgeClass = match($dep['refund_status']) {
                                'held'               => 'badge-info',
                                'refunded'           => 'badge-success',
                                'partially_refunded' => 'badge-warning',
                                'forfeited'          => 'badge-danger',
                                default              => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($dep['refund_status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if ($dep['refund_status'] === 'held'): ?>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openRefundModal(<?= htmlspecialchars(json_encode($dep), ENT_QUOTES) ?>)">
                                Settle / Refund
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openRefundModal(<?= htmlspecialchars(json_encode($dep), ENT_QUOTES) ?>)">
                                Details
                            </button>
                            <?php endif; ?>
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

<!-- Refund / Settlement Modal -->
<div id="refundModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalDepositTitle" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Settle Security Deposit</h3>
            <button type="button" onclick="closeRefundModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="refundForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="background: var(--slate-50); padding: 1rem; border-radius: 6px; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.25rem;">
                        <span style="color: var(--slate-500);">Tenant:</span>
                        <strong id="modalTenant"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.25rem;">
                        <span style="color: var(--slate-500);">Total Caution Money:</span>
                        <strong id="modalAmount"></strong>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Settlement Type <span style="color: var(--rose-500);">*</span></label>
                    <select name="refund_status" id="refundStatusSelect" class="form-control" required onchange="calculateAmounts()">
                        <option value="refunded">Full Refund to Tenant</option>
                        <option value="partially_refunded">Partial Refund (With Repair/Late Fee Deductions)</option>
                        <option value="forfeited">Complete Forfeiture (Default/Breach)</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Refunded Amount (₹) <span style="color: var(--rose-500);">*</span></label>
                        <input type="number" step="0.01" name="refund_amount" id="modalRefundAmount" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Deductions / Damage (₹)</label>
                        <input type="number" step="0.01" name="adjusted_amount" id="modalAdjustedAmount" class="form-control" value="0.00">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Settlement / Refund Date <span style="color: var(--rose-500);">*</span></label>
                    <input type="date" name="refund_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Reason for Deductions / Adjustments</label>
                    <input type="text" name="adjustment_reason" class="form-control" placeholder="e.g. Wall painting, unpaid electricity, plumbing repair">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Transaction Remarks / Bank Ref</label>
                    <input type="text" name="remarks" class="form-control" placeholder="e.g. NEFT Reference #987654321">
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeRefundModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Settlement</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentDeposit = null;

function openRefundModal(dep) {
    currentDeposit = dep;
    document.getElementById('modalDepositTitle').innerText = 'Settle Deposit ' + dep.deposit_number;
    document.getElementById('modalTenant').innerText = dep.tenant_name + ' (' + dep.tenant_code + ')';
    document.getElementById('modalAmount').innerText = '₹' + parseFloat(dep.amount).toFixed(2);
    document.getElementById('modalRefundAmount').value = parseFloat(dep.amount).toFixed(2);
    document.getElementById('modalAdjustedAmount').value = '0.00';
    document.getElementById('refundForm').action = '/deposits/refund/' + dep.id;
    document.getElementById('refundModal').style.display = 'flex';
}

function closeRefundModal() {
    document.getElementById('refundModal').style.display = 'none';
}

function calculateAmounts() {
    if (!currentDeposit) return;
    const total = parseFloat(currentDeposit.amount);
    const status = document.getElementById('refundStatusSelect').value;
    if (status === 'refunded') {
        document.getElementById('modalRefundAmount').value = total.toFixed(2);
        document.getElementById('modalAdjustedAmount').value = '0.00';
    } else if (status === 'forfeited') {
        document.getElementById('modalRefundAmount').value = '0.00';
        document.getElementById('modalAdjustedAmount').value = total.toFixed(2);
    } else if (status === 'partially_refunded') {
        document.getElementById('modalRefundAmount').value = (total / 2).toFixed(2);
        document.getElementById('modalAdjustedAmount').value = (total / 2).toFixed(2);
    }
}
</script>

<?= $this->endSection() ?>
