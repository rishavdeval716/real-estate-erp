<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/portal/tenant">Self-Service Portals</a> &rsaquo;
            <span>Resident Tenant Portal</span>
        </div>
        <h1 class="page-title">Resident Tenant Portal</h1>
        <p class="page-subtitle">View active tenancy contracts, pending rent demand notices, download receipts, and log service tickets.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openTenantReqModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Submit Resident Request
        </button>
    </div>
</div>

<!-- Tenant Banner -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase;">Resident Account</div>
            <div style="font-size: 1.75rem; font-weight: 700;">
                <?= esc($tenant ? $tenant['full_name'] : (session()->get('user_name') ?? 'Resident Tenant')) ?>
            </div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Tenant Code: <strong><?= esc($tenant['tenant_code'] ?? 'TEN-000001') ?></strong> &bull;
                Email: <?= esc($tenant['email'] ?? session()->get('user_email')) ?> &bull;
                Mobile: <?= esc($tenant['mobile'] ?? 'N/A') ?>
            </div>
        </div>
        <div style="display: flex; gap: 1.5rem; text-align: center;">
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Active Leases</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #38bdf8;"><?= count($leases) ?></div>
            </div>
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Rent Receipts</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #34d399;"><?= count($collections) ?></div>
            </div>
        </div>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Active Leases -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">My Active Leases & Tenancies</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Agreement #</th>
                            <th>Leased Premises</th>
                            <th>Unit #</th>
                            <th>Term Dates</th>
                            <th>Monthly Rent</th>
                            <th>Lock-in</th>
                            <th>Status</th>
                            <th style="text-align: right;">Voucher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leases)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--slate-500);">No active leases found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($leases as $l): ?>
                        <tr>
                            <td>
                                <strong><?= esc($l['agreement_number']) ?></strong>
                            </td>
                            <td><?= esc($l['property_title']) ?></td>
                            <td>Unit <?= esc($l['unit_number'] ?? 'All') ?></td>
                            <td><?= date('d M Y', strtotime($l['start_date'])) ?> to <?= date('d M Y', strtotime($l['end_date'])) ?></td>
                            <td>
                                <strong style="color: var(--slate-800);">₹<?= number_format((float)$l['monthly_rent'], 2) ?></strong>
                            </td>
                            <td><?= (int)$l['lock_in_period_months'] ?> months</td>
                            <td>
                                <span class="badge <?= $l['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>" style="text-transform: capitalize;">
                                    <?= esc($l['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="/leases/voucher/<?= $l['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print Agreement</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rent Demands & Collections -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Outstanding Rent Demands -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Rent Demand Notices</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Demand #</th>
                                <th>Period</th>
                                <th>Due Date</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($demands)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No rent demands issued.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($demands as $dem): ?>
                            <tr>
                                <td>
                                    <a href="/rent-demands/view/<?= $dem['id'] ?>" style="font-weight: 600; color: var(--primary-600);">
                                        <?= esc($dem['demand_number']) ?>
                                    </a>
                                </td>
                                <td><?= esc($dem['billing_period']) ?></td>
                                <td><?= date('d M Y', strtotime($dem['due_date'])) ?></td>
                                <td>
                                    <strong style="color: <?= (float)$dem['balance_amount'] > 0 ? 'var(--rose-600)' : 'var(--emerald-600)' ?>;">
                                        ₹<?= number_format((float)$dem['balance_amount'], 2) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge <?= $dem['status'] === 'paid' ? 'badge-success' : 'badge-danger' ?>" style="text-transform: capitalize;">
                                        <?= esc($dem['status']) ?>
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

        <!-- Rent Receipts -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Payment Receipts</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount Paid</th>
                                <th style="text-align: right;">Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($collections)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No payment receipts recorded.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($collections as $col): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($col['collection_number']) ?></strong>
                                </td>
                                <td><?= date('d M Y', strtotime($col['payment_date'])) ?></td>
                                <td>
                                    <strong style="color: var(--emerald-600);">₹<?= number_format((float)$col['amount'], 2) ?></strong>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/rent-collections/receipt/<?= $col['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print</a>
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

    <!-- Maintenance Tickets & Complaints -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Maintenance Work Orders -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Maintenance Tickets</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No maintenance tickets logged.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($tickets as $t): ?>
                            <tr>
                                <td><strong><?= esc($t['ticket_number']) ?></strong></td>
                                <td><?= ucfirst(esc($t['category'])) ?></td>
                                <td><span class="badge badge-light"><?= esc($t['priority']) ?></span></td>
                                <td><span class="badge <?= $t['status'] === 'closed' ? 'badge-success' : 'badge-warning' ?>"><?= esc($t['status']) ?></span></td>
                                <td style="text-align: right;">
                                    <a href="/maintenance/view/<?= $t['id'] ?>" class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resident Inquiries / Requests -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Resident Service Requests</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No portal requests submitted.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><strong><?= esc($r['request_code']) ?></strong></td>
                                <td><?= esc($r['request_type']) ?></td>
                                <td><?= esc($r['subject']) ?></td>
                                <td>
                                    <span class="badge badge-info"><?= esc($r['status']) ?></span>
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
</div>

<!-- Modal: Submit Resident Request -->
<div id="tenantReqModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Submit Resident Service Request</h3>
            <button type="button" onclick="closeTenantReqModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/portal/request/submit">
            <?= csrf_field() ?>
            <input type="hidden" name="portal_type" value="tenant">

            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Request Category <span style="color: var(--rose-500);">*</span></label>
                    <select name="request_type" class="form-control" required>
                        <option value="Urgent Repair Escalation">Urgent Repair Escalation</option>
                        <option value="Security Deposit Settlement Inquiry">Security Deposit Settlement Inquiry</option>
                        <option value="Lease Renewal Notification">Notice of Lease Renewal / Term Extension</option>
                        <option value="Notice of Vacating Premises">Notice of Departure / Vacating Premises</option>
                        <option value="Parking Slot Assignment">Parking Slot / Sticker Request</option>
                        <option value="General Resident Grievance">General Resident Grievance</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Subject <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="subject" class="form-control" placeholder="Brief summary" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Message & Details <span style="color: var(--rose-500);">*</span></label>
                    <textarea name="details" class="form-control" rows="4" placeholder="Explain your request or provide dates..." required></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeTenantReqModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTenantReqModal() {
    document.getElementById('tenantReqModal').style.display = 'flex';
}
function closeTenantReqModal() {
    document.getElementById('tenantReqModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
