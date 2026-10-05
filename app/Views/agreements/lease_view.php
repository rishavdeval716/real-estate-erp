<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Lease <?= esc($lease['agreement_number']) ?></span>
        </div>
        <h1 class="page-title">Lease Agreement <?= esc($lease['agreement_number']) ?></h1>
        <p class="page-subtitle">Tenancy details, financial covenants, linked security deposits, and rental history.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/leases/voucher/<?= $lease['id'] ?>" class="btn btn-secondary" target="_blank">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Voucher
        </a>

        <?php if ($lease['status'] === 'draft'): ?>
        <form method="POST" action="/leases/activate/<?= $lease['id'] ?>" onsubmit="return confirm('Activate this lease agreement? Unit availability will update to Rented.');" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Activate Lease
            </button>
        </form>
        <?php endif; ?>

        <?php if (in_array($lease['status'], ['active', 'expiring_soon'])): ?>
        <form method="POST" action="/leases/renew/<?= $lease['id'] ?>" onsubmit="return confirm('Renew this lease for another term with <?= esc($lease['rent_escalation_pct']) ?>% rent escalation?');" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="color: var(--primary-600); border-color: var(--primary-300);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                Renew Lease (+<?= esc($lease['rent_escalation_pct']) ?>%)
            </button>
        </form>

        <form method="POST" action="/leases/terminate/<?= $lease['id'] ?>" onsubmit="return confirm('Terminate this lease? Unit availability will return to Available.');" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="color: var(--rose-600); border-color: var(--rose-300);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Terminate Lease
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Header Summary Card -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.05em;">Lease Agreement Reference</div>
            <div style="font-size: 1.75rem; font-weight: 700;"><?= esc($lease['agreement_number']) ?></div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Tenant: <strong><?= esc($lease['tenant_name']) ?></strong> (<?= esc($lease['tenant_code']) ?>) &bull;
                Property: <strong><?= esc($lease['property_title']) ?></strong> <?= !empty($lease['unit_number']) ? '(Unit ' . esc($lease['unit_number']) . ')' : '' ?>
            </div>
        </div>

        <div style="display: flex; gap: 2rem; align-items: center;">
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--slate-400);">Monthly Rental</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--emerald-400);">₹<?= number_format((float)$lease['monthly_rent'], 2) ?></div>
            </div>
            <div>
                <?php
                $badgeClass = match($lease['status']) {
                    'active'        => 'badge-success',
                    'expiring_soon' => 'badge-warning',
                    'draft'         => 'badge-secondary',
                    'renewed'       => 'badge-info',
                    'expired', 'terminated' => 'badge-danger',
                    default         => 'badge-light',
                };
                ?>
                <span class="badge <?= $badgeClass ?>" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; text-transform: capitalize;">
                    <?= str_replace('_', ' ', esc($lease['status'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Details Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Left Column: Terms, Clauses & History -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Contract Terms Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Contractual Tenancy Terms</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Agreement Type</div>
                        <div style="font-weight: 600; text-transform: capitalize; color: var(--slate-800);"><?= esc($lease['agreement_type']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Term Duration</div>
                        <div style="font-weight: 600; color: var(--slate-800);"><?= date('d M Y', strtotime($lease['start_date'])) ?> to <?= date('d M Y', strtotime($lease['end_date'])) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Lock-in Period</div>
                        <div style="font-weight: 600; color: var(--slate-800);"><?= (int)$lease['lock_in_period_months'] ?> Months</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Notice Period</div>
                        <div style="font-weight: 600; color: var(--slate-800);"><?= (int)$lease['notice_period_days'] ?> Days</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Payment Due Day</div>
                        <div style="font-weight: 600; color: var(--slate-800);">Day <?= (int)$lease['payment_due_day'] ?> of each month</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Late Fee Penalty</div>
                        <div style="font-weight: 600; color: var(--rose-600);">₹<?= number_format((float)$lease['late_fee_amount'], 2) ?> / day</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Escalation Clause</div>
                        <div style="font-weight: 600; color: var(--emerald-600);"><?= esc($lease['rent_escalation_pct']) ?>% (<?= esc($lease['escalation_frequency']) ?>)</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Maintenance Fee</div>
                        <div style="font-weight: 600; color: var(--slate-800);">₹<?= number_format((float)$lease['maintenance_charges'], 2) ?>/mo</div>
                    </div>
                </div>

                <?php if (!empty($lease['terms_conditions'])): ?>
                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--slate-100);">
                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.25rem;">Special Clauses & Conditions:</div>
                    <div style="font-size: 0.85rem; color: var(--slate-600); line-height: 1.5; white-space: pre-line; background: var(--slate-50); padding: 0.75rem; border-radius: 4px;">
                        <?= esc($lease['terms_conditions']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Linked Rent Demands Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Monthly Rent Demands & Billing</h3>
                <a href="/rent-demands" class="btn btn-sm btn-secondary">All Demands</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Demand #</th>
                                <th>Billing Period</th>
                                <th>Due Date</th>
                                <th>Total Demanded</th>
                                <th>Paid</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($demands)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 1.5rem;">No rent demands generated yet.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($demands as $d): ?>
                            <tr>
                                <td>
                                    <a href="/rent-demands/view/<?= $d['id'] ?>" style="font-weight: 500; color: var(--primary-600);">
                                        <?= esc($d['demand_number']) ?>
                                    </a>
                                </td>
                                <td><?= esc($d['billing_period']) ?></td>
                                <td><?= date('d M Y', strtotime($d['due_date'])) ?></td>
                                <td>₹<?= number_format((float)$d['total_amount'], 2) ?></td>
                                <td style="color: var(--emerald-600);">₹<?= number_format((float)$d['paid_amount'], 2) ?></td>
                                <td style="font-weight: 600; color: <?= (float)$d['balance_amount'] > 0 ? 'var(--rose-600)' : 'var(--slate-700)' ?>;">
                                    ₹<?= number_format((float)$d['balance_amount'], 2) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $d['status'] === 'paid' ? 'badge-success' : ($d['status'] === 'overdue' ? 'badge-danger' : 'badge-warning') ?>" style="text-transform: capitalize;">
                                        <?= esc($d['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rental History Log -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Rental & Escalation History</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <?php if (empty($history)): ?>
                <div style="color: var(--slate-400); text-align: center; padding: 1rem;">No prior historical lease records.</div>
                <?php else: ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($history as $h): ?>
                    <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--slate-100); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-weight: 600; color: var(--slate-800);"><?= esc($h['status']) ?></span>
                            <?php if (!empty($h['previous_rent'])): ?>
                            <span style="font-size: 0.8rem; color: var(--slate-500);">(Escalated from ₹<?= number_format((float)$h['previous_rent'], 2) ?> &rarr; ₹<?= number_format((float)$h['current_rent'], 2) ?>)</span>
                            <?php else: ?>
                            <span style="font-size: 0.8rem; color: var(--slate-500);">(Rent: ₹<?= number_format((float)$h['current_rent'], 2) ?>)</span>
                            <?php endif; ?>
                            <div style="font-size: 0.75rem; color: var(--slate-400);"><?= esc($h['notes']) ?></div>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--slate-500);">
                            <?= date('d M Y', strtotime($h['start_date'])) ?> to <?= !empty($h['end_date']) ? date('d M Y', strtotime($h['end_date'])) : 'Present' ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Tenant & Security Deposit Cards -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Tenant Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Tenant Profile</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--slate-800); margin-bottom: 0.25rem;">
                    <?= esc($lease['tenant_name']) ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-bottom: 1rem;">
                    Code: <?= esc($lease['tenant_code']) ?>
                </div>

                <div style="font-size: 0.85rem; color: var(--slate-600); margin-bottom: 0.5rem;">
                    <strong>Mobile:</strong> <?= esc($lease['tenant_mobile'] ?? 'N/A') ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--slate-600); margin-bottom: 1rem;">
                    <strong>Email:</strong> <?= esc($lease['tenant_email'] ?? 'N/A') ?>
                </div>

                <a href="/tenants/view/<?= $lease['tenant_id'] ?>" class="btn btn-sm btn-secondary" style="width: 100%; text-align: center;">
                    View Complete Tenant Profile &rarr;
                </a>
            </div>
        </div>

        <!-- Security Deposit Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Security Deposit</h3>
                <a href="/deposits" class="btn btn-sm btn-secondary">Deposits</a>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <?php if ($deposit): ?>
                <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Deposit Number</div>
                <div style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.75rem;"><?= esc($deposit['deposit_number']) ?></div>

                <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Original Deposit Amount</div>
                <div style="font-size: 1.25rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.75rem;">₹<?= number_format((float)$deposit['amount'], 2) ?></div>

                <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Refundable / Held</div>
                <div style="font-weight: 600; color: var(--emerald-600); margin-bottom: 0.75rem;">₹<?= number_format((float)$deposit['refundable_amount'], 2) ?></div>

                <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Deductions / Adjusted</div>
                <div style="font-weight: 600; color: var(--rose-600); margin-bottom: 0.75rem;">₹<?= number_format((float)$deposit['adjusted_amount'], 2) ?></div>

                <div style="font-size: 0.8rem; color: var(--slate-500); text-transform: uppercase;">Status</div>
                <span class="badge <?= $deposit['refund_status'] === 'held' ? 'badge-info' : 'badge-success' ?>" style="text-transform: capitalize;">
                    <?= str_replace('_', ' ', esc($deposit['refund_status'])) ?>
                </span>
                <?php else: ?>
                <div style="color: var(--slate-400); text-align: center; padding: 1rem;">No security deposit recorded.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Property Unit Details Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Premises Info</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="font-weight: 600; color: var(--slate-800); margin-bottom: 0.25rem;">
                    <?= esc($lease['property_title']) ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.75rem;">
                    Unit: <?= esc($lease['unit_number'] ?? 'Entire Property') ?>
                </div>
                <a href="/properties/view/<?= $lease['property_id'] ?>" class="btn btn-sm btn-secondary" style="width: 100%; text-align: center;">
                    View Property Details &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
