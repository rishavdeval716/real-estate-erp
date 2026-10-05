<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/portal/partner">Self-Service Portals</a> &rsaquo;
            <span>Channel Partner Portal</span>
        </div>
        <h1 class="page-title">Channel Partner & Broker Portal</h1>
        <p class="page-subtitle">Track referred buyer leads, active unit sales, approved commissions, TDS deductions, and live inventory.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openPartnerReqModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Submit Commission / Support Request
        </button>
    </div>
</div>

<!-- Partner Profile Banner -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase;">Channel Partner Network</div>
            <div style="font-size: 1.75rem; font-weight: 700;">
                <?= esc(session()->get('user_name') ?? 'Premier Channel Partner') ?>
            </div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Account: <strong><?= esc(session()->get('user_email')) ?></strong> &bull;
                RERA Reg: <strong>A51900012345</strong> &bull; Tier: <strong>Gold Partner</strong>
            </div>
        </div>
        <div style="display: flex; gap: 1.5rem; text-align: center;">
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Referred Leads</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #38bdf8;"><?= count($leads) ?></div>
            </div>
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Commissions</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #34d399;"><?= count($commissions) ?></div>
            </div>
        </div>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- 1. Commission & Payout Status -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Broker Commission Tracking & Status</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking Ref #</th>
                            <th>Customer Name</th>
                            <th>Calculation Type</th>
                            <th>Commission Value</th>
                            <th>Net Payable</th>
                            <th>Approval Status</th>
                            <th>Disbursement Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">No commission records generated yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($commissions as $comm): ?>
                        <tr>
                            <td>
                                <strong><?= esc($comm['booking_number']) ?></strong>
                            </td>
                            <td><?= esc($comm['first_name'] . ' ' . $comm['last_name']) ?></td>
                            <td><?= esc($comm['rule_type'] ?? 'Percentage') ?> (<?= esc($comm['rule_value'] ?? '2.0%') ?>)</td>
                            <td>
                                <?php $commAmt = $comm['commission_amount'] ?? $comm['amount'] ?? 0; ?>
                                <strong style="color: var(--slate-800);">₹<?= number_format((float)$commAmt, 2) ?></strong>
                            </td>
                            <td>
                                <strong style="color: var(--emerald-600);">₹<?= number_format((float)($comm['net_amount'] ?? $commAmt), 2) ?></strong>
                            </td>
                            <td>
                                <?php
                                $cBadge = match($comm['status']) {
                                    'Paid'     => 'badge-success',
                                    'Approved' => 'badge-primary',
                                    'Pending'  => 'badge-warning',
                                    default    => 'badge-secondary',
                                };
                                ?>
                                <span class="badge <?= $cBadge ?>"><?= esc($comm['status']) ?></span>
                            </td>
                            <td>
                                <?php $pDate = $comm['paid_date'] ?? null; ?>
                                <?= !empty($pDate) ? date('d M Y', strtotime($pDate)) : 'Pending payout' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Referred Leads & Conversion Status -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Referred Leads & Pipeline Status</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Lead Ref #</th>
                            <th>Prospective Buyer</th>
                            <th>Contact</th>
                            <th>Channel Source</th>
                            <th>Pipeline Stage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--slate-500);">No registered leads currently active.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($leads as $l): ?>
                        <tr>
                            <td><strong><?= esc($l['lead_code'] ?? $l['lead_number'] ?? 'LEAD') ?></strong></td>
                            <td><?= esc($l['first_name'] . ' ' . $l['last_name']) ?></td>
                            <td><?= esc($l['phone']) ?></td>
                            <td><?= esc($l['source_name'] ?? 'Channel Partner') ?></td>
                            <td>
                                <span class="badge badge-light"><?= esc($l['lead_stage'] ?? $l['stage'] ?? 'New') ?></span>
                            </td>
                            <td>
                                <?php $lStat = $l['lead_status'] ?? $l['status'] ?? 'Active'; ?>
                                <span class="badge <?= $lStat === 'Converted' || $lStat === 'Won' ? 'badge-success' : 'badge-info' ?>"><?= esc($lStat) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Available Inventory to Sell -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Live Available Inventory for Client Pitching</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Unit #</th>
                            <th>Property Project</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Super Built-up Area</th>
                            <th>Base Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($availableUnits)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--slate-500);">No units currently marked available.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($availableUnits as $u): ?>
                        <tr>
                            <td><strong>Unit <?= esc($u['unit_number']) ?></strong></td>
                            <td><?= esc($u['property_title']) ?></td>
                            <td><?= esc($u['property_type_name'] ?? 'Residential Flat') ?></td>
                            <td>Floor <?= esc($u['floor'] ?? '1') ?></td>
                            <td><?= esc($u['super_built_up_area'] ?? '1100') ?> sq.ft</td>
                            <td>
                                <strong style="color: var(--primary-600);">₹<?= number_format((float)($u['base_price'] ?? 0), 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-success">Available</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Support & Commission Inquiries -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">My Partner Requests & Inquiries</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Request Type</th>
                            <th>Subject</th>
                            <th>Submitted Date</th>
                            <th>Status</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No requests submitted yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><strong><?= esc($r['request_code']) ?></strong></td>
                            <td><span class="badge badge-light"><?= esc($r['request_type']) ?></span></td>
                            <td><?= esc($r['subject']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></td>
                            <td>
                                <span class="badge <?= $r['status'] === 'completed' ? 'badge-success' : 'badge-info' ?>"><?= esc($r['status']) ?></span>
                            </td>
                            <td><?= esc($r['admin_notes'] ?? 'Processing by partner desk') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Submit Partner Request -->
<div id="partnerReqModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Submit Partner Request</h3>
            <button type="button" onclick="closePartnerReqModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/portal/request/submit">
            <?= csrf_field() ?>
            <input type="hidden" name="portal_type" value="partner">

            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Request Type <span style="color: var(--rose-500);">*</span></label>
                    <select name="request_type" class="form-control" required>
                        <option value="Commission Payout Claim">Commission Payout Claim</option>
                        <option value="Lead Attribution Dispute">Lead Attribution / Registration Claim</option>
                        <option value="Marketing Collateral Request">Marketing Collateral / High-Res Floor Plans</option>
                        <option value="TDS Form 16A Request">TDS Form 16A Certificate Request</option>
                        <option value="General Partner Support">General Partner Support</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Subject <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="subject" class="form-control" placeholder="Brief subject" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Details & Justification <span style="color: var(--rose-500);">*</span></label>
                    <textarea name="details" class="form-control" rows="4" placeholder="Mention booking code, buyer name, or commission reference..." required></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closePartnerReqModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPartnerReqModal() {
    document.getElementById('partnerReqModal').style.display = 'flex';
}
function closePartnerReqModal() {
    document.getElementById('partnerReqModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
