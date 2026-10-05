<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/rent-demands">Rent Demands</a> &rsaquo;
            <span><?= esc($demand['demand_number']) ?></span>
        </div>
        <h1 class="page-title">Rent Demand Notice <?= esc($demand['demand_number']) ?></h1>
        <p class="page-subtitle">Billing breakdown for <?= date('F Y', strtotime($demand['billing_period'] . '-01')) ?>, payment receipts, and balance status.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Notice
        </button>

        <?php if ((float)$demand['balance_amount'] > 0): ?>
        <button type="button" class="btn btn-primary" onclick="openPaymentModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Record Rent Collection (₹<?= number_format((float)$demand['balance_amount'], 2) ?>)
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Demand Summary Header Card -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase;">Billing Period</div>
            <div style="font-size: 1.75rem; font-weight: 700;"><?= date('F Y', strtotime($demand['billing_period'] . '-01')) ?></div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Tenant: <strong><?= esc($demand['tenant_name']) ?></strong> (<?= esc($demand['tenant_code']) ?>) &bull;
                Property: <strong><?= esc($demand['property_title']) ?></strong> <?= !empty($demand['unit_number']) ? '(Unit ' . esc($demand['unit_number']) . ')' : '' ?>
            </div>
        </div>

        <div style="display: flex; gap: 2rem; align-items: center;">
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--slate-400);">Balance Due</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: <?= (float)$demand['balance_amount'] > 0 ? 'var(--rose-400)' : 'var(--emerald-400)' ?>;">
                    ₹<?= number_format((float)$demand['balance_amount'], 2) ?>
                </div>
            </div>
            <div>
                <?php
                $badgeClass = match($demand['status']) {
                    'paid'           => 'badge-success',
                    'partially_paid' => 'badge-info',
                    'overdue'        => 'badge-danger',
                    default          => 'badge-warning',
                };
                ?>
                <span class="badge <?= $badgeClass ?>" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; text-transform: capitalize;">
                    <?= str_replace('_', ' ', esc($demand['status'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Left: Statement Breakdown & Collections -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Line Items Breakdown -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Rent Demand Bill Items</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Particulars / Description</th>
                            <th style="text-align: right;">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Base Monthly Rent</td>
                            <td style="text-align: right; font-weight: 500;">₹<?= number_format((float)$demand['base_rent'], 2) ?></td>
                        </tr>
                        <tr>
                            <td>Maintenance Charges</td>
                            <td style="text-align: right; font-weight: 500;">₹<?= number_format((float)$demand['maintenance'], 2) ?></td>
                        </tr>
                        <?php if ((float)$demand['other_charges'] > 0): ?>
                        <tr>
                            <td>Other Utilities & Surcharges</td>
                            <td style="text-align: right; font-weight: 500;">₹<?= number_format((float)$demand['other_charges'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ((float)$demand['tax'] > 0): ?>
                        <tr>
                            <td>GST Tax (18% on Commercial Lease)</td>
                            <td style="text-align: right; font-weight: 500; color: var(--indigo-600);">₹<?= number_format((float)$demand['tax'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ((float)$demand['late_fee'] > 0): ?>
                        <tr>
                            <td style="color: var(--rose-600); font-weight: 600;">Assessed Overdue Penalty / Late Fee</td>
                            <td style="text-align: right; font-weight: 600; color: var(--rose-600);">+₹<?= number_format((float)$demand['late_fee'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr style="background: var(--slate-50); font-size: 1rem;">
                            <td style="font-weight: 700;">Total Demand Amount</td>
                            <td style="text-align: right; font-weight: 700; color: var(--slate-900);">₹<?= number_format((float)$demand['total_amount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: var(--emerald-600);">Paid to Date</td>
                            <td style="text-align: right; font-weight: 600; color: var(--emerald-600);">-₹<?= number_format((float)$demand['paid_amount'], 2) ?></td>
                        </tr>
                        <tr style="background: #fff1f2; font-size: 1.05rem;">
                            <td style="font-weight: 700; color: var(--rose-700);">Current Outstanding Balance</td>
                            <td style="text-align: right; font-weight: 700; color: var(--rose-700);">₹<?= number_format((float)$demand['balance_amount'], 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recorded Collections List -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Payment Collections & Receipts</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Amount Paid</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($collections)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 2rem;">No collections recorded yet against this demand.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($collections as $c): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-600);"><?= esc($c['collection_number']) ?></strong>
                                </td>
                                <td><?= date('d M Y', strtotime($c['payment_date'])) ?></td>
                                <td>
                                    <span class="badge badge-light"><?= esc($c['payment_method']) ?></span>
                                </td>
                                <td><?= esc($c['transaction_reference'] ?? '—') ?></td>
                                <td>
                                    <span style="font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$c['amount'], 2) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/rent-collections/receipt/<?= $c['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print Receipt</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Tenancy Reference -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Tenancy Context</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Lease Reference</div>
                <div style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.75rem;">
                    <a href="/leases/view/<?= $demand['lease_id'] ?>"><?= esc($demand['agreement_number']) ?></a>
                </div>

                <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Tenant Name</div>
                <div style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.75rem;">
                    <a href="/tenants/view/<?= $demand['tenant_id'] ?>"><?= esc($demand['tenant_name']) ?></a>
                </div>

                <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Payment Due Date</div>
                <div style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.75rem;">
                    <?= date('d M Y', strtotime($demand['due_date'])) ?>
                </div>

                <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Premises</div>
                <div style="font-weight: 500; color: var(--slate-700);">
                    <?= esc($demand['property_title']) ?> &bull; Unit <?= esc($demand['unit_number'] ?? 'N/A') ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Issuer Information</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem; font-size: 0.85rem; color: var(--slate-600);">
                <strong><?= esc($company['name'] ?? 'Real Estate ERP Enterprise Ltd.') ?></strong><br>
                <?= esc($company['address'] ?? '') ?>, <?= esc($company['city'] ?? '') ?><br>
                Email: <?= esc($company['email'] ?? '') ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Record Rent Collection -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Record Rent Collection</h3>
            <button type="button" onclick="closePaymentModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/rent-collections/store">
            <?= csrf_field() ?>
            <input type="hidden" name="rent_demand_id" value="<?= $demand['id'] ?>">

            <div class="card-body" style="padding: 1.5rem;">
                <div style="background: var(--slate-50); padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.85rem;">
                    <div>Demand Notice: <strong><?= esc($demand['demand_number']) ?></strong></div>
                    <div>Remaining Balance: <strong style="color: var(--rose-600);">₹<?= number_format((float)$demand['balance_amount'], 2) ?></strong></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Collection Amount (₹) <span style="color: var(--rose-500);">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control" max="<?= (float)$demand['balance_amount'] ?>" value="<?= (float)$demand['balance_amount'] ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Payment Date <span style="color: var(--rose-500);">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Payment Method <span style="color: var(--rose-500);">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="NEFT">NEFT</option>
                            <option value="RTGS">RTGS</option>
                            <option value="IMPS">IMPS</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                            <option value="Online Gateway">Online Gateway</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Transaction / UTR Reference</label>
                    <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. UTR12345678 or Cheque #987654">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Internal Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Optional collection note">
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closePaymentModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment & Issue Receipt</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
