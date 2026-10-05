<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/portal/owner">Self-Service Portals</a> &rsaquo;
            <span>Customer & Property Owner Portal</span>
        </div>
        <h1 class="page-title">Property Owner & Buyer Portal</h1>
        <p class="page-subtitle">Access your booked real estate inventory, sales agreements, payment receipts, tax invoices, and request NOCs.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openRequestModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Submit Service / NOC Request
        </button>
    </div>
</div>

<!-- Customer Profile Banner -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase;">Authenticated Buyer / Owner Account</div>
            <div style="font-size: 1.75rem; font-weight: 700;">
                <?= esc($customer ? ($customer['first_name'] . ' ' . $customer['last_name']) : (session()->get('user_name') ?? 'Valued Customer')) ?>
            </div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Customer Code: <strong><?= esc($customer['customer_code'] ?? 'CUS-MASTER') ?></strong> &bull;
                Email: <?= esc($customer['email'] ?? session()->get('user_email')) ?> &bull;
                Mobile: <?= esc($customer['phone'] ?? 'N/A') ?>
            </div>
        </div>
        <div style="display: flex; gap: 1.5rem; text-align: center;">
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Properties Booked</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #38bdf8;"><?= count($bookings) ?></div>
            </div>
            <div style="background: rgba(255,255,255,0.08); padding: 0.75rem 1.25rem; border-radius: 8px;">
                <div style="font-size: 0.8rem; color: var(--slate-300);">Receipts Issued</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #34d399;"><?= count($receipts) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Grid -->
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <?php if (!empty($ownedProperties)): ?>
    <!-- Landlord Portfolio & Rental Management Section -->
    <div class="card" style="border: 2px solid var(--primary-light);">
        <div class="card-header" style="background: var(--primary-light); border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--primary);">Landlord Portfolio & Managed Properties</h3>
                <p style="margin: 0; font-size: 0.82rem; color: var(--slate-600);">Real-time ownership asset view, active tenant leases, and verified rental collections.</p>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span class="badge badge-primary">Total Rental Collected: ₹<?= number_format($totalRentalIncome, 2) ?></span>
                <?php if (!empty($ownerProfile)): ?>
                <a href="/owners/statement/<?= $ownerProfile['id'] ?>" class="btn btn-sm btn-primary" target="_blank">Print Statement</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Property Code</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Area</th>
                            <th>Valuation</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ownedProperties as $op): ?>
                        <tr>
                            <td><strong style="color: var(--primary);"><?= esc($op['property_code']) ?></strong></td>
                            <td><?= esc($op['title']) ?></td>
                            <td><?= esc($op['property_type_name'] ?? 'Residential') ?></td>
                            <td><?= esc($op['location_city'] ?? 'Prime Hub') ?></td>
                            <td><?= number_format((float)$op['area'], 0) ?> Sq.Ft.</td>
                            <td><strong>₹<?= number_format((float)$op['price'], 2) ?></strong></td>
                            <td><span class="badge badge-success"><?= esc($op['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($tenantLeases)): ?>
            <div style="padding: 1rem 1.25rem 0.5rem; border-top: 1px solid var(--slate-200);">
                <h4 style="font-size: 0.92rem; font-weight: 700; margin: 0 0 0.5rem; color: var(--slate-800);">Active Tenant Leases</h4>
                <div class="table-container">
                    <table class="table" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th>Agreement #</th>
                                <th>Unit #</th>
                                <th>Tenant Name</th>
                                <th>Contact</th>
                                <th>Monthly Rent</th>
                                <th>Period</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tenantLeases as $tl): ?>
                            <tr>
                                <td><strong><?= esc($tl['agreement_number']) ?></strong></td>
                                <td>Unit <?= esc($tl['unit_number']) ?></td>
                                <td><?= esc($tl['tenant_fname']) ?></td>
                                <td><?= esc($tl['tenant_phone']) ?></td>
                                <td><strong style="color: var(--success);">₹<?= number_format((float)$tl['monthly_rent'], 2) ?></strong></td>
                                <td><?= date('d M Y', strtotime($tl['start_date'])) ?> to <?= date('d M Y', strtotime($tl['end_date'])) ?></td>
                                <td><span class="badge badge-success"><?= esc($tl['status']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 1. Owned / Booked Units Card -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">My Booked Real Estate Units</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking Ref #</th>
                            <th>Property Name</th>
                            <th>Unit #</th>
                            <th>Booking Date</th>
                            <th>Total Consideration</th>
                            <th>Booking Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No active property bookings found for your profile.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary-600);"><?= esc($b['booking_number']) ?></strong>
                            </td>
                            <td><?= esc($b['property_title']) ?></td>
                            <td>Unit <?= esc($b['unit_number']) ?></td>
                            <td><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                            <td>
                                <span style="font-weight: 700; color: var(--slate-800);">₹<?= number_format((float)$b['final_amount'], 2) ?></span>
                            </td>
                            <td>
                                <?php $bStatus = $b['booking_status'] ?? $b['status'] ?? 'Confirmed'; ?>
                                <span class="badge <?= $bStatus === 'Confirmed' ? 'badge-success' : 'badge-info' ?>">
                                    <?= esc($bStatus) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="/bookings/voucher/<?= $b['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Booking Voucher</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Sales Agreements & Legal Deeds -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Sales Agreements & Legal Deeds</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Agreement #</th>
                            <th>Execution Date</th>
                            <th>Possession Handover Target</th>
                            <th>Legal Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($agreements)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--slate-500);">No legal agreement records executed yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($agreements as $ag): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary-600);"><?= esc($ag['agreement_number']) ?></strong>
                            </td>
                            <td><?= date('d M Y', strtotime($ag['agreement_date'])) ?></td>
                            <td><?= !empty($ag['possession_target_date']) ? date('d M Y', strtotime($ag['possession_target_date'])) : 'Per schedule' ?></td>
                            <td>
                                <?php $agStatus = $ag['agreement_status'] ?? $ag['status'] ?? 'Executed'; ?>
                                <span class="badge <?= $agStatus === 'Signed' || $agStatus === 'Executed' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= esc($agStatus) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="/agreements/view/<?= $ag['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">View Agreement</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Official Receipts & Invoices Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Official Receipts -->
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
                                <th>Amount</th>
                                <th style="text-align: right;">Print</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($receipts)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No payment receipts generated.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($receipts as $rc): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-600);"><?= esc($rc['receipt_number']) ?></strong>
                                </td>
                                <td><?= date('d M Y', strtotime($rc['receipt_date'])) ?></td>
                                <td>
                                    <strong style="color: var(--emerald-600);">₹<?= number_format((float)$rc['amount'], 2) ?></strong>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/receipts/view/<?= $rc['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Demand Invoices -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Demand Invoices</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No invoices issued.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($inv['invoice_number']) ?></strong>
                                </td>
                                <td><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                <td>₹<?= number_format((float)$inv['total_amount'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $inv['status'] === 'Paid' ? 'badge-success' : 'badge-warning' ?>"><?= esc($inv['status']) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/invoices/view/<?= $inv['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">View</a>
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

    <!-- 4. Self-Service Requests & NOC History -->
    <div class="card">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">My Submitted Inquiries & NOC Requests</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Request Type</th>
                            <th>Subject</th>
                            <th>Date Submitted</th>
                            <th>Status</th>
                            <th>Admin Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1.5rem; color: var(--slate-400);">No self-service requests submitted yet.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td>
                                <strong><?= esc($req['request_code']) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-light"><?= esc($req['request_type']) ?></span>
                            </td>
                            <td><?= esc($req['subject']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <span class="badge <?= $req['status'] === 'completed' ? 'badge-success' : 'badge-info' ?>" style="text-transform: capitalize;">
                                    <?= esc($req['status']) ?>
                                </span>
                            </td>
                            <td style="color: var(--slate-600);"><?= esc($req['admin_notes'] ?? 'Under review by executive desk') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Submit Service / NOC Request -->
<div id="requestModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Submit Service / NOC Request</h3>
            <button type="button" onclick="closeRequestModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/portal/request/submit">
            <?= csrf_field() ?>
            <input type="hidden" name="portal_type" value="customer">

            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Request Category <span style="color: var(--rose-500);">*</span></label>
                    <select name="request_type" class="form-control" required>
                        <option value="NOC for Bank Loan">NOC for Bank Loan / Mortgage</option>
                        <option value="Possession Status Inquiry">Possession Status & Inspection Inquiry</option>
                        <option value="Certified Account Statement">Certified Customer Account Statement</option>
                        <option value="Stamped Agreement Copy">Duplicate Stamped Agreement Copy</option>
                        <option value="Transfer of Title / Assignment">Transfer of Title / Resale Clearance</option>
                        <option value="General Customer Grievance">General Customer Inquiry</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Subject <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="subject" class="form-control" placeholder="Brief subject" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Details & Justification <span style="color: var(--rose-500);">*</span></label>
                    <textarea name="details" class="form-control" rows="4" placeholder="Mention unit number, financial institution, or specific notes..." required></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeRequestModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRequestModal() {
    document.getElementById('requestModal').style.display = 'flex';
}
function closeRequestModal() {
    document.getElementById('requestModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
