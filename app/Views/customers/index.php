<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/customers">Sales</a> &rsaquo;
            <span>Customers</span>
        </div>
        <h1 class="page-title">Customer Directory</h1>
        <p class="page-subtitle">Manage buyer profiles, KYC verification documents, and transaction portfolios.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/customers/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Customer
        </a>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/customers" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, code, email, phone, PAN/ID..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">KYC Status</label>
                <select name="kyc_status" class="form-control">
                    <option value="">All KYC Statuses</option>
                    <option value="Pending" <?= $filters['kyc_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Verified" <?= $filters['kyc_status'] === 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Rejected" <?= $filters['kyc_status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/customers" class="btn btn-secondary" title="Reset Filters">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>ID / PAN Proof</th>
                        <th>KYC Status</th>
                        <th>Bookings</th>
                        <th>Registered</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--slate-500);">
                                No customer records found. Convert a qualified lead or create a new customer.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);">
                                        <a href="/customers/view/<?= $c['id'] ?>" style="color: var(--brand-primary); text-decoration: none;">
                                            <?= esc($c['first_name'] . ' ' . $c['last_name']) ?>
                                        </a>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500); font-family: monospace;">
                                        <?= esc($c['customer_code']) ?>
                                        <?php if (!empty($c['lead_code'])): ?>
                                            &bull; From Lead <a href="/leads/view/<?= $c['lead_id'] ?>" style="color: var(--slate-600);"><?= esc($c['lead_code']) ?></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;"><?= esc($c['phone']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($c['email'] ?: 'No email') ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem;"><?= esc($c['city'] ?: '-') ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($c['state'] ?: '') ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; font-weight: 500;"><?= esc($c['id_proof_type'] ?: '-') ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500); font-family: monospace;"><?= esc($c['id_proof_number'] ?: 'Not provided') ?></div>
                                </td>
                                <td>
                                    <?php
                                    $kycBadge = 'badge-secondary';
                                    if ($c['kyc_status'] === 'Verified') $kycBadge = 'badge-success';
                                    elseif ($c['kyc_status'] === 'Rejected') $kycBadge = 'badge-danger';
                                    elseif ($c['kyc_status'] === 'Pending') $kycBadge = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $kycBadge ?>"><?= esc($c['kyc_status']) ?></span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-800); font-weight: 600;">
                                        <?= (int)($c['total_bookings'] ?? 0) ?> active
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--slate-500);">
                                    <?= date('d M Y', strtotime($c['created_at'])) ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                        <a href="/customers/view/<?= $c['id'] ?>" class="btn btn-sm btn-secondary" title="View Transaction Profile">
                                            View
                                        </a>
                                        <a href="/customers/edit/<?= $c['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Customer">
                                            Edit
                                        </a>
                                        <a href="/bookings/create?customer_id=<?= $c['id'] ?>" class="btn btn-sm btn-primary" title="New Booking">
                                            Book
                                        </a>
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
