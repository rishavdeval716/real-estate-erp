<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/bookings">Bookings</a> &rsaquo;
            <span><?= esc($booking['booking_number']) ?></span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <h1 class="page-title" style="margin: 0;">Booking <?= esc($booking['booking_number']) ?></h1>
            <?php
            $badge = 'badge-secondary';
            if ($booking['booking_status'] === 'Confirmed') $badge = 'badge-success';
            elseif ($booking['booking_status'] === 'Cancelled') $badge = 'badge-danger';
            elseif ($booking['booking_status'] === 'Pending Confirmation') $badge = 'badge-warning';
            elseif ($booking['booking_status'] === 'Completed') $badge = 'badge-primary';
            ?>
            <span class="badge <?= $badge ?>" style="font-size: 0.9rem; padding: 0.35rem 0.75rem;"><?= esc($booking['booking_status']) ?></span>
        </div>
        <p class="page-subtitle">
            Booking Date: <strong><?= date('d M Y', strtotime($booking['booking_date'])) ?></strong>
            &bull; Managed by <strong><?= esc($booking['executive_name'] ?? 'System') ?></strong>
        </p>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/bookings/voucher/<?= $booking['id'] ?>" target="_blank" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Voucher
        </a>

        <?php if ($booking['booking_status'] !== 'Cancelled' && $booking['total_outstanding'] > 0): ?>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('paymentModal').style.display='block';">
                + Record Payment
            </button>
        <?php endif; ?>

        <?php if ($booking['booking_status'] === 'Draft' || $booking['booking_status'] === 'Pending Confirmation'): ?>
            <form method="POST" action="/bookings/confirm/<?= $booking['id'] ?>" onsubmit="return confirm('Confirm this booking? This will officially lock the unit as BOOKED.');" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success">
                    Confirm Booking
                </button>
            </form>
        <?php endif; ?>

        <?php if ($booking['booking_status'] !== 'Cancelled'): ?>
            <button type="button" class="btn btn-danger" onclick="document.getElementById('cancelModal').style.display='block';">
                Cancel Booking
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<!-- Dynamic Financial KPI Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Booking Value</div>
            <div class="metric-value">₹<?= number_format($booking['final_amount'], 2) ?></div>
            <div class="metric-meta">Agreed final consideration</div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Total Paid</div>
            <div class="metric-value">₹<?= number_format($booking['total_paid'], 2) ?></div>
            <div class="metric-meta"><?= $booking['final_amount'] > 0 ? round(($booking['total_paid'] / $booking['final_amount']) * 100) : 0 ?>% cleared</div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Total Outstanding</div>
            <div class="metric-value">₹<?= number_format($booking['total_outstanding'], 2) ?></div>
            <div class="metric-meta">Balance receivable</div>
        </div>
    </div>
    <div class="metric-card danger">
        <div>
            <div class="metric-label">Overdue Amount</div>
            <div class="metric-value">₹<?= number_format($booking['overdue_amount'], 2) ?></div>
            <div class="metric-meta">Past milestone dates</div>
        </div>
    </div>
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Next Due Date</div>
            <div class="metric-value" style="font-size: 1.25rem;">
                <?= $booking['next_due_date'] ? date('d M Y', strtotime($booking['next_due_date'])) : 'All Cleared' ?>
            </div>
            <div class="metric-meta">Upcoming milestone</div>
        </div>
    </div>
</div>

<!-- Details Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Customer Details Card -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Customer Information</h3>
            <a href="/customers/view/<?= $booking['customer_id'] ?>" class="btn btn-sm btn-secondary">Profile</a>
        </div>
        <div class="card-body">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--slate-900); margin-bottom: 0.25rem;">
                <?= esc($booking['first_name'] . ' ' . $booking['last_name']) ?>
            </div>
            <div style="font-size: 0.8rem; font-family: monospace; color: var(--slate-500); margin-bottom: 1rem;">
                Code: <?= esc($booking['customer_code']) ?>
                <?php if (!empty($booking['lead_code'])): ?>
                    &bull; Lead: <a href="/leads/view/<?= $booking['lead_id'] ?>"><?= esc($booking['lead_code']) ?></a>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.85rem;">
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Phone</span>
                    <strong><?= esc($booking['customer_phone']) ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Email</span>
                    <strong><?= esc($booking['customer_email'] ?: 'N/A') ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Identity Proof</span>
                    <strong><?= esc($booking['id_proof_type'] ?: 'N/A') ?>: <?= esc($booking['id_proof_number'] ?: '-') ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">KYC Status</span>
                    <span class="badge <?= $booking['kyc_status'] === 'Verified' ? 'badge-success' : 'badge-warning' ?>"><?= esc($booking['kyc_status']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit & Property Card -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Property & Unit Details</h3>
            <span class="badge badge-primary">Unit: <?= esc($booking['unit_number']) ?></span>
        </div>
        <div class="card-body">
            <div style="font-size: 1.1rem; font-weight: 700; color: var(--slate-900); margin-bottom: 0.25rem;">
                <?= esc($booking['project_name'] ?: $booking['property_title']) ?>
            </div>
            <div style="font-size: 0.8rem; color: var(--slate-500); margin-bottom: 1rem;">
                Property: <?= esc($booking['property_title'] ?? 'Direct Project Unit') ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; font-size: 0.85rem;">
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Flat Configuration</span>
                    <strong><?= esc($booking['flat_type'] ?? 'N/A') ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Floor</span>
                    <strong>Floor <?= esc($booking['floor_number'] ?? $booking['floor'] ?? 'N/A') ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Carpet Area</span>
                    <strong><?= esc($booking['carpet_area'] ?? '-') ?> sq.ft</strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Base Price</span>
                    <strong>₹<?= number_format($booking['base_price'], 2) ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Discount</span>
                    <strong>₹<?= number_format($booking['discount'], 2) ?></strong>
                </div>
                <div>
                    <span style="color: var(--slate-500); display: block; font-size: 0.75rem;">Current Unit State</span>
                    <span class="badge badge-secondary"><?= esc($booking['current_unit_status']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Construction Payment Schedule & Milestones -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Construction-Linked Payment Schedule</h3>
            <span style="font-size: 0.75rem; color: var(--slate-500);">Standard 8-milestone payment plan totaling agreed booking value</span>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('paymentModal').style.display='block';">
                + Allocate Payment
            </button>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Milestone Name</th>
                        <th>Due Date</th>
                        <th>Share (%)</th>
                        <th>Milestone Amount</th>
                        <th>Paid Amount</th>
                        <th>Remaining</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($booking['milestones'])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">
                                No payment schedule generated for this booking.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $idx = 1; foreach ($booking['milestones'] as $m): ?>
                            <tr>
                                <td><?= $idx++ ?></td>
                                <td style="font-weight: 600;"><?= esc($m['milestone_name']) ?></td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($m['due_date'])) ?></td>
                                <td style="font-weight: 600;"><?= number_format($m['percentage'], 1) ?>%</td>
                                <td style="font-weight: 700;">₹<?= number_format($m['amount'], 2) ?></td>
                                <td style="color: #16a34a; font-weight: 600;">₹<?= number_format($m['paid_amount'], 2) ?></td>
                                <td style="color: #dc2626; font-weight: 600;">₹<?= number_format($m['remaining_amount'], 2) ?></td>
                                <td>
                                    <?php
                                    $mBadge = 'badge-secondary';
                                    if ($m['status'] === 'Paid') $mBadge = 'badge-success';
                                    elseif ($m['status'] === 'Partially Paid') $mBadge = 'badge-warning';
                                    elseif ($m['status'] === 'Overdue') $mBadge = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $mBadge ?>"><?= esc($m['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recorded Payments Table -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Recorded Payments (<?= count($booking['payments']) ?>)</h3>
        <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('paymentModal').style.display='block';">
            + Record Payment
        </button>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Payment Date</th>
                        <th>Milestone Target</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference / Cheque</th>
                        <th>Status</th>
                        <th>Receipt</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($booking['payments'])): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">
                                No payments recorded yet against this booking.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($booking['payments'] as $p): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--brand-primary);"><?= esc($p['payment_number']) ?></td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                <td style="font-size: 0.85rem;"><?= esc($p['milestone_name'] ?: 'General Allocation') ?></td>
                                <td style="font-weight: 700; color: var(--slate-900);">₹<?= number_format($p['amount'], 2) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($p['payment_method']) ?></span></td>
                                <td style="font-size: 0.8rem; font-family: monospace; color: var(--slate-600);"><?= esc($p['transaction_reference'] ?: $p['cheque_number'] ?: '-') ?></td>
                                <td>
                                    <span class="badge <?= $p['status'] === 'Received' ? 'badge-success' : ($p['status'] === 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $rec = null;
                                    foreach ($booking['receipts'] as $r) {
                                        if ($r['payment_id'] == $p['id']) { $rec = $r; break; }
                                    }
                                    ?>
                                    <?php if ($rec): ?>
                                        <a href="/receipts/view/<?= $rec['id'] ?>" target="_blank" class="btn btn-sm btn-secondary" title="View Official Receipt">
                                            <?= esc($rec['receipt_number']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="font-size: 0.75rem; color: var(--slate-400);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($p['status'] !== 'Cancelled'): ?>
                                        <form method="POST" action="/payments/cancel/<?= $p['id'] ?>" onsubmit="return confirm('Cancel this payment record? This will reverse milestone allocation.');" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sales Agreement & Invoices Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Sales Agreement Section -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Sales Agreement</h3>
            <?php if (empty($booking['agreement'])): ?>
                <a href="/agreements/create?booking_id=<?= $booking['id'] ?>" class="btn btn-sm btn-primary">+ Generate Agreement</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($booking['agreement'])): ?>
                <div style="text-align: center; padding: 1.5rem; color: var(--slate-500);">
                    No legal sales agreement created yet for this booking.
                    <div style="margin-top: 0.5rem;">
                        <a href="/agreements/create?booking_id=<?= $booking['id'] ?>" class="btn btn-sm btn-secondary">Generate Now</a>
                    </div>
                </div>
            <?php else: ?>
                <?php $agr = $booking['agreement']; ?>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-size: 1.1rem; font-weight: 700; font-family: monospace; color: var(--brand-primary);">
                            <?= esc($agr['agreement_number']) ?>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--slate-500);"><?= esc($agr['agreement_type']) ?> &bull; Date: <?= date('d M Y', strtotime($agr['agreement_date'])) ?></div>
                    </div>
                    <span class="badge <?= $agr['agreement_status'] === 'Signed' ? 'badge-success' : 'badge-warning' ?>">
                        <?= esc($agr['agreement_status']) ?>
                    </span>
                </div>
                <div style="font-weight: 700; margin-bottom: 1rem;">Agreed Value: ₹<?= number_format($agr['total_value'], 2) ?></div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/agreements/view/<?= $agr['id'] ?>" class="btn btn-secondary">View Agreement</a>
                    <?php if ($agr['agreement_status'] !== 'Signed'): ?>
                        <form method="POST" action="/agreements/sign/<?= $agr['id'] ?>" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success">Mark as Signed</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invoices Section -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Invoices</h3>
            <a href="/invoices/create?booking_id=<?= $booking['id'] ?>" class="btn btn-sm btn-primary">+ Generate Invoice</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($booking['invoices'])): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No invoices issued yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($booking['invoices'] as $inv): ?>
                                <tr>
                                    <td>
                                        <a href="/invoices/view/<?= $inv['id'] ?>" style="font-weight: 600; text-decoration: none; font-family: monospace;">
                                            <?= esc($inv['invoice_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                                    <td style="font-weight: 600;">₹<?= number_format($inv['total_amount'], 2) ?></td>
                                    <td style="color: #dc2626; font-weight: 600;">₹<?= number_format($inv['balance_amount'], 2) ?></td>
                                    <td><span class="badge badge-secondary"><?= esc($inv['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Commission Foundation & Status History Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Commission Section -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Agent / Broker Commissions</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('commissionModal').style.display='block';">
                + Calculate Commission
            </button>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Rule</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($booking['commissions'])): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No commission records for this booking.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($booking['commissions'] as $com): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?= esc($com['agent_name']) ?></td>
                                    <td style="font-size: 0.8rem;"><?= esc($com['rule_name'] ?: 'Direct Fixed') ?></td>
                                    <td style="font-weight: 700;">₹<?= number_format($com['commission_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $com['status'] === 'Paid' ? 'badge-success' : ($com['status'] === 'Approved' ? 'badge-primary' : 'badge-warning') ?>">
                                            <?= esc($com['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($com['status'] === 'Pending'): ?>
                                            <form method="POST" action="/commissions/approve/<?= $com['id'] ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                            </form>
                                        <?php elseif ($com['status'] === 'Approved'): ?>
                                            <form method="POST" action="/commissions/mark-paid/<?= $com['id'] ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Booking Status History Timeline -->
    <div class="card">
        <div class="card-header">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Booking Status Audit History</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Transition</th>
                            <th>Changed By</th>
                            <th>Remarks</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($booking['status_history'])): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No status transitions logged.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($booking['status_history'] as $hist): ?>
                                <tr>
                                    <td>
                                        <span style="color: var(--slate-500);"><?= esc($hist['old_status'] ?: 'Start') ?></span>
                                        &rarr;
                                        <strong><?= esc($hist['new_status']) ?></strong>
                                    </td>
                                    <td><?= esc($hist['changed_by_name'] ?? 'System') ?></td>
                                    <td style="color: var(--slate-600);"><?= esc($hist['remarks'] ?: '-') ?></td>
                                    <td style="font-size: 0.75rem; color: var(--slate-500);"><?= date('d M Y H:i', strtotime($hist['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Record Payment -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 520px; width: 90%; margin: 5% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Record Transaction Payment</h3>
            <button type="button" onclick="document.getElementById('paymentModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/payments/store">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div style="background: var(--slate-100); padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.85rem;">
                <div>Total Outstanding Balance: <strong style="color: #dc2626;">₹<?= number_format($booking['total_outstanding'], 2) ?></strong></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Amount (₹) <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" max="<?= $booking['total_outstanding'] ?>" name="amount" class="form-control" required value="<?= $booking['total_outstanding'] ?>">
                </div>
                <div>
                    <label class="form-label">Payment Date <span style="color: red;">*</span></label>
                    <input type="date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Payment Method <span style="color: red;">*</span></label>
                    <select name="payment_method" class="form-control" required>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="NEFT">NEFT</option>
                        <option value="RTGS">RTGS</option>
                        <option value="IMPS">IMPS</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Cash">Cash</option>
                        <option value="Online Gateway">Online Gateway</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Payment Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Received">Received (Auto-allocates & issues Receipt)</option>
                        <option value="Pending">Pending Clearance</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Assign to Milestone (Optional)</label>
                <select name="payment_schedule_item_id" class="form-control">
                    <option value="">-- Auto-Allocate to Earliest Unpaid Milestone --</option>
                    <?php foreach ($booking['milestones'] as $ms): ?>
                        <option value="<?= $ms['id'] ?>">
                            <?= esc($ms['milestone_name']) ?> (Remaining: ₹<?= number_format($ms['remaining_amount'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Transaction Reference</label>
                    <input type="text" name="transaction_reference" class="form-control" placeholder="UTR / Ref ID">
                </div>
                <div>
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="Issuing Bank">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('paymentModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cancel Booking -->
<div id="cancelModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 480px; width: 90%; margin: 7% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #dc2626;">Cancel Property Booking</h3>
            <button type="button" onclick="document.getElementById('cancelModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/bookings/cancel/<?= $booking['id'] ?>">
            <?= csrf_field() ?>
            <p style="font-size: 0.85rem; color: var(--slate-600); margin-bottom: 1rem;">
                Cancelling this booking will change the status to <strong>Cancelled</strong>, release Unit <strong><?= esc($booking['unit_number']) ?></strong> back to <strong>Available</strong>, and preserve non-destructive audit history.
            </p>
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Cancellation Reason <span style="color: red;">*</span></label>
                <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Specify formal reason for cancellation..."></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('cancelModal').style.display='none';">Go Back</button>
                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Assign Commission -->
<div id="commissionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 480px; width: 90%; margin: 7% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Calculate Agent Commission</h3>
            <button type="button" onclick="document.getElementById('commissionModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/commissions/store">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Select Agent / Sales Executive <span style="color: red;">*</span></label>
                <select name="agent_user_id" class="form-control" required>
                    <?php foreach ($agents as $ag): ?>
                        <option value="<?= $ag['id'] ?>" <?= $booking['sales_executive_id'] == $ag['id'] ? 'selected' : '' ?>>
                            <?= esc($ag['name']) ?> (<?= esc($ag['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Commission Rule (Optional)</label>
                <select name="commission_rule_id" class="form-control">
                    <option value="">-- Custom Manual Amount --</option>
                    <?php foreach ($commissionRules as $cr): ?>
                        <option value="<?= $cr['id'] ?>">
                            <?= esc($cr['name']) ?> (<?= esc($cr['commission_type']) ?>: <?= esc($cr['commission_value']) ?><?= $cr['commission_type'] === 'Percentage' ? '%' : '' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Manual Commission Amount (₹)</label>
                <input type="number" step="0.01" name="commission_amount" class="form-control" placeholder="Leave blank if using rule above">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Commission terms..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('commissionModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Commission</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
