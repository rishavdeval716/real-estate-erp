<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Sales Dashboard</span>
        </div>
        <h1 class="page-title">Sales & Financial Transactions Dashboard</h1>
        <p class="page-subtitle">Real-time revenue realization, milestone collections, outstanding balances, and active booking metrics.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/bookings/create" class="btn btn-primary">
            + New Booking
        </a>
        <a href="/bookings" class="btn btn-secondary">
            View All Bookings
        </a>
    </div>
</div>

<!-- 10 Dynamic MySQL Sales Metrics -->
<div class="metric-grid crm-grid" style="margin-bottom: 1.5rem;">
    <!-- Metric 1: Total Booking Value -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Booking Value</div>
            <div class="metric-value">₹<?= number_format($summary['total_booking_value'], 2) ?></div>
            <div class="metric-meta">From confirmed/completed sales</div>
        </div>
    </div>

    <!-- Metric 2: Total Collected -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Total Collected</div>
            <div class="metric-value">₹<?= number_format($summary['total_collected'], 2) ?></div>
            <div class="metric-meta">Verified received payments</div>
        </div>
    </div>

    <!-- Metric 3: Total Outstanding -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Total Outstanding</div>
            <div class="metric-value">₹<?= number_format($summary['total_outstanding'], 2) ?></div>
            <div class="metric-meta">Remaining receivables</div>
        </div>
    </div>

    <!-- Metric 4: Overdue Amount -->
    <div class="metric-card danger">
        <div>
            <div class="metric-label">Overdue Amount</div>
            <div class="metric-value">₹<?= number_format($summary['overdue_amount'], 2) ?></div>
            <div class="metric-meta">Milestones past due date</div>
        </div>
    </div>

    <!-- Metric 5: Upcoming Payments -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Upcoming Payments</div>
            <div class="metric-value">₹<?= number_format($summary['upcoming_amount'], 2) ?></div>
            <div class="metric-meta"><?= $summary['upcoming_count'] ?> milestones in next 30 days</div>
        </div>
    </div>
</div>

<!-- Booking Status Breakdown Cards -->
<div class="metric-grid crm-grid" style="margin-bottom: 2rem;">
    <!-- Metric 6: Total Bookings -->
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Total Bookings</div>
            <div class="metric-value"><?= number_format($summary['total_bookings']) ?></div>
            <div class="metric-meta">All-time bookings</div>
        </div>
    </div>

    <!-- Metric 7: Confirmed Bookings -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Confirmed Bookings</div>
            <div class="metric-value"><?= number_format($summary['confirmed_bookings']) ?></div>
            <div class="metric-meta">Units locked as Booked</div>
        </div>
    </div>

    <!-- Metric 8: Pending Bookings -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Pending Bookings</div>
            <div class="metric-value"><?= number_format($summary['pending_bookings']) ?></div>
            <div class="metric-meta">Drafts & awaiting clearance</div>
        </div>
    </div>

    <!-- Metric 9: Cancelled Bookings -->
    <div class="metric-card danger">
        <div>
            <div class="metric-label">Cancelled Bookings</div>
            <div class="metric-value"><?= number_format($summary['cancelled_bookings']) ?></div>
            <div class="metric-meta">Units returned to Available</div>
        </div>
    </div>

    <!-- Metric 10: Active Agreements -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Active Agreements</div>
            <div class="metric-value"><?= number_format($summary['active_agreements']) ?></div>
            <div class="metric-meta">Fully executed & Signed</div>
        </div>
    </div>
</div>

<!-- Revenue Collection Progress Banner -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <?php
        $realizedPct = $summary['total_booking_value'] > 0 
            ? round(($summary['total_collected'] / $summary['total_booking_value']) * 100, 1) 
            : 0;
        ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <div>
                <strong style="font-size: 1rem; color: var(--slate-900);">Realized Capital Collection Progress</strong>
                <div style="font-size: 0.85rem; color: var(--slate-500);">
                    Collected ₹<?= number_format($summary['total_collected'], 2) ?> of ₹<?= number_format($summary['total_booking_value'], 2) ?> total booked consideration
                </div>
            </div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a;">
                <?= $realizedPct ?>%
            </div>
        </div>
        <div style="background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden;">
            <div style="width: <?= min(100, $realizedPct) ?>%; background: linear-gradient(90deg, #10b981, #059669); height: 100%;"></div>
        </div>
    </div>
</div>

<!-- Grid: Recent Bookings & Upcoming Due Milestones -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Recent Bookings Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Recent Property Bookings</h3>
            <a href="/bookings" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Customer</th>
                            <th>Unit</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentBookings)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No bookings yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentBookings as $bk): ?>
                                <tr>
                                    <td>
                                        <a href="/bookings/view/<?= $bk['id'] ?>" style="font-weight: 700; font-family: monospace; color: var(--brand-primary); text-decoration: none;">
                                            <?= esc($bk['booking_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($bk['first_name'] . ' ' . $bk['last_name']) ?></td>
                                    <td>Unit <?= esc($bk['unit_number']) ?></td>
                                    <td style="font-weight: 600;">₹<?= number_format($bk['final_amount'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $bk['booking_status'] === 'Confirmed' ? 'badge-success' : ($bk['booking_status'] === 'Cancelled' ? 'badge-danger' : 'badge-warning') ?>">
                                            <?= esc($bk['booking_status']) ?>
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

    <!-- Upcoming Milestone Payments (Next 30 Days) -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Upcoming Milestone Due Dates (Next 30 Days)</h3>
            <span class="badge badge-warning"><?= count($upcomingMilestones) ?> Due</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-container">
                <table class="table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Due Date</th>
                            <th>Customer / Booking</th>
                            <th>Milestone</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingMilestones)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No pending milestones due in the next 30 days.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingMilestones as $up): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #dc2626;"><?= date('d M Y', strtotime($up['due_date'])) ?></td>
                                    <td>
                                        <div style="font-weight: 600;"><?= esc($up['first_name'] . ' ' . $up['last_name']) ?></div>
                                        <div style="font-size: 0.75rem; font-family: monospace; color: var(--slate-500);"><?= esc($up['booking_number']) ?> &bull; <?= esc($up['phone']) ?></div>
                                    </td>
                                    <td><?= esc($up['milestone_name']) ?></td>
                                    <td style="font-weight: 700; color: var(--slate-900);">₹<?= number_format($up['remaining_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Recent Received Payments</h3>
        <a href="/payments" class="btn btn-sm btn-secondary">View All Payments</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPayments)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">No payments recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentPayments as $rp): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--brand-primary);"><?= esc($rp['payment_number']) ?></td>
                                <td style="font-family: monospace;"><?= esc($rp['booking_number']) ?></td>
                                <td><?= esc($rp['first_name'] . ' ' . $rp['last_name']) ?></td>
                                <td><?= date('d M Y', strtotime($rp['payment_date'])) ?></td>
                                <td style="font-weight: 700; color: #16a34a;">₹<?= number_format($rp['amount'], 2) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($rp['payment_method']) ?></span></td>
                                <td style="font-family: monospace; font-size: 0.75rem;"><?= esc($rp['transaction_reference'] ?: '-') ?></td>
                                <td>
                                    <span class="badge <?= $rp['status'] === 'Received' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= esc($rp['status']) ?>
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

<?= $this->endSection() ?>
