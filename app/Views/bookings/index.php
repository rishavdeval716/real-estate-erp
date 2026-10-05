<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/bookings">Sales</a> &rsaquo;
            <span>Bookings</span>
        </div>
        <h1 class="page-title">Booking Management</h1>
        <p class="page-subtitle">Track unit bookings, confirmations, payment schedules, and sales vouchers.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/bookings/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create New Booking
        </a>
    </div>
</div>

<!-- Search & Multi-Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/bookings" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; align-items: flex-end;">
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Booking #, customer, unit, property..." value="<?= esc($filters['search']) ?>">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">Status</label>
                <select name="booking_status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Draft" <?= $filters['booking_status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Pending Confirmation" <?= $filters['booking_status'] === 'Pending Confirmation' ? 'selected' : '' ?>>Pending Confirmation</option>
                    <option value="Confirmed" <?= $filters['booking_status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Cancelled" <?= $filters['booking_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="Completed" <?= $filters['booking_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">Project</label>
                <select name="project_id" class="form-control">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $pj): ?>
                        <option value="<?= $pj['id'] ?>" <?= $filters['project_id'] == $pj['id'] ? 'selected' : '' ?>><?= esc($pj['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">Sales Executive</label>
                <select name="sales_executive_id" class="form-control">
                    <option value="">All Executives</option>
                    <?php foreach ($executives as $ex): ?>
                        <option value="<?= $ex['id'] ?>" <?= $filters['sales_executive_id'] == $ex['id'] ? 'selected' : '' ?>><?= esc($ex['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from']) ?>">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 0.2rem;">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to']) ?>">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary" style="flex: 1;">Filter</button>
                <a href="/bookings" class="btn btn-secondary" title="Reset Filters">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Project / Property</th>
                        <th>Unit</th>
                        <th>Booking Date</th>
                        <th>Total Value</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No bookings found matching criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>
                                    <a href="/bookings/view/<?= $b['id'] ?>" style="font-weight: 700; color: var(--brand-primary); text-decoration: none; font-family: monospace;">
                                        <?= esc($b['booking_number']) ?>
                                    </a>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);">By <?= esc($b['executive_name'] ?? 'System') ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);">
                                        <a href="/customers/view/<?= $b['customer_id'] ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($b['first_name'] . ' ' . $b['last_name']) ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($b['customer_phone']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.9rem;"><?= esc($b['project_name'] ?: $b['property_title']) ?></div>
                                    <?php if (!empty($b['property_title']) && $b['property_title'] !== $b['project_name']): ?>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($b['property_title']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-800); font-weight: 600; font-family: monospace;">
                                        Unit <?= esc($b['unit_number']) ?>
                                    </span>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($b['flat_type'] ?? '') ?></div>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    <?= date('d M Y', strtotime($b['booking_date'])) ?>
                                </td>
                                <td style="font-weight: 700; color: var(--slate-900);">
                                    ₹<?= number_format($b['final_amount'], 2) ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #16a34a;">₹<?= number_format((float)($b['total_paid'] ?? 0), 2) ?></div>
                                    <?php 
                                    $pct = $b['final_amount'] > 0 ? round(((float)$b['total_paid'] / (float)$b['final_amount']) * 100) : 0;
                                    ?>
                                    <div style="width: 70px; background: #e2e8f0; height: 4px; border-radius: 2px; margin-top: 4px; overflow: hidden;">
                                        <div style="width: <?= min(100, $pct) ?>%; background: #16a34a; height: 100%;"></div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $badge = 'badge-secondary';
                                    if ($b['booking_status'] === 'Confirmed') $badge = 'badge-success';
                                    elseif ($b['booking_status'] === 'Cancelled') $badge = 'badge-danger';
                                    elseif ($b['booking_status'] === 'Pending Confirmation') $badge = 'badge-warning';
                                    elseif ($b['booking_status'] === 'Completed') $badge = 'badge-primary';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= esc($b['booking_status']) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                        <a href="/bookings/view/<?= $b['id'] ?>" class="btn btn-sm btn-secondary">Details</a>
                                        <a href="/bookings/voucher/<?= $b['id'] ?>" class="btn btn-sm btn-secondary" target="_blank" title="Printable Voucher">Voucher</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pager)): ?>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-200);">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
