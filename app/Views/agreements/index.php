<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/agreements">Sales</a> &rsaquo;
            <span>Sales Agreements</span>
        </div>
        <h1 class="page-title">Sales Agreements</h1>
        <p class="page-subtitle">Draft, manage, sign, and print statutory buyer agreements and sale deeds.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/agreements/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Generate Agreement
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/agreements" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Agreement #, booking #, customer, unit..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="agreement_status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Draft" <?= $filters['agreement_status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Pending Signature" <?= $filters['agreement_status'] === 'Pending Signature' ? 'selected' : '' ?>>Pending Signature</option>
                    <option value="Signed" <?= $filters['agreement_status'] === 'Signed' ? 'selected' : '' ?>>Signed</option>
                    <option value="Cancelled" <?= $filters['agreement_status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/agreements" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Agreements Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Agreement #</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Agreed Value</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agreements)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No sales agreements found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($agreements as $agr): ?>
                            <tr>
                                <td>
                                    <a href="/agreements/view/<?= $agr['id'] ?>" style="font-weight: 700; color: var(--brand-primary); text-decoration: none; font-family: monospace;">
                                        <?= esc($agr['agreement_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="/bookings/view/<?= $agr['booking_id'] ?>" style="font-family: monospace; color: var(--slate-700);">
                                        <?= esc($agr['booking_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($agr['first_name'] . ' ' . $agr['last_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($agr['customer_phone']) ?></div>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-800); font-weight: 600;">
                                        Unit <?= esc($agr['unit_number']) ?>
                                    </span>
                                </td>
                                <td><?= esc($agr['agreement_type']) ?></td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($agr['agreement_date'])) ?></td>
                                <td style="font-weight: 700;">₹<?= number_format($agr['total_value'], 2) ?></td>
                                <td>
                                    <?php
                                    $stClass = 'badge-secondary';
                                    if ($agr['agreement_status'] === 'Signed') $stClass = 'badge-success';
                                    elseif ($agr['agreement_status'] === 'Cancelled') $stClass = 'badge-danger';
                                    elseif ($agr['agreement_status'] === 'Pending Signature') $stClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $stClass ?>"><?= esc($agr['agreement_status']) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="/agreements/view/<?= $agr['id'] ?>" class="btn btn-sm btn-secondary">Document</a>
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
