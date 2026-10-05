<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leases">Rentals</a> &rsaquo;
            <span>Rent Collections</span>
        </div>
        <h1 class="page-title">Rent Collections & Receipts</h1>
        <p class="page-subtitle">Historical log of recorded tenant rent payments, banking references, and official payment receipts.</p>
    </div>
    <div>
        <a href="/rent-demands" class="btn btn-secondary">
            &larr; View Rent Demands
        </a>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Collections Count</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total_count']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Collections Value</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$kpi['total_collections'], 2) ?></div>
    </div>
</div>

<!-- Search Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/rent-collections" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 3; min-width: 250px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search Collections</label>
                <input type="text" name="search" class="form-control" placeholder="Receipt #, demand #, tenant name, reference..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Search
                </button>
                <a href="/rent-collections" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Collections Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Payment Date</th>
                        <th>Demand Notice #</th>
                        <th>Billing Month</th>
                        <th>Tenant</th>
                        <th>Lease Agreement</th>
                        <th>Payment Method</th>
                        <th>Reference / UTR</th>
                        <th>Amount Paid</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collections)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No rent collection records found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collections as $c): ?>
                    <tr>
                        <td>
                            <a href="/rent-collections/receipt/<?= $c['id'] ?>" style="font-weight: 600; color: var(--primary-600);" target="_blank">
                                <?= esc($c['collection_number']) ?>
                            </a>
                        </td>
                        <td><?= date('d M Y', strtotime($c['payment_date'])) ?></td>
                        <td>
                            <a href="/rent-demands/view/<?= $c['rent_demand_id'] ?>" style="color: var(--slate-700);">
                                <?= esc($c['demand_number']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-light"><?= esc($c['billing_period']) ?></span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($c['tenant_name']) ?></div>
                            <span class="badge badge-light" style="font-size: 0.7rem;"><?= esc($c['tenant_code']) ?></span>
                        </td>
                        <td>
                            <a href="/leases/view/<?= $c['lease_id'] ?>" style="color: var(--slate-600);">
                                <?= esc($c['agreement_number']) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-light"><?= esc($c['payment_method']) ?></span>
                        </td>
                        <td><?= esc($c['transaction_reference'] ?? '—') ?></td>
                        <td>
                            <strong style="color: var(--emerald-600);">₹<?= number_format((float)$c['amount'], 2) ?></strong>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="/rent-collections/receipt/<?= $c['id'] ?>" class="btn btn-sm btn-secondary" target="_blank" title="Print Official Receipt">Print</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
    <div class="card-footer" style="padding: 1rem;">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
