<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Pricing</span>
        </div>
        <h1 class="page-title">Property Pricing & Valuation</h1>
        <p class="page-subtitle">Manage base rates, price per sq.ft., market valuations, discounts, and historical pricing schedules</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('pricing.create', session()->get('permissions') ?? [])): ?>
            <a href="/pricing/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Valuation Schedule
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Target Property / Unit</th>
                        <th>Base Price</th>
                        <th>Price / Sq.Ft</th>
                        <th>Market Valuation</th>
                        <th>Negotiated Price</th>
                        <th>Discount</th>
                        <th>Effective From</th>
                        <th>Remarks</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pricings)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No pricing valuation records found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pricings as $pr): ?>
                            <tr>
                                <td>
                                    <?php if ($pr['unit_id']): ?>
                                        <div style="font-weight: 700; color: var(--primary);">
                                            <a href="/units/view/<?= esc($pr['unit_id']) ?>" style="color: inherit; text-decoration: none;">
                                                Unit <?= esc($pr['unit_number']) ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($pr['property_title'] ?? '') ?></div>
                                    <?php else: ?>
                                        <div style="font-weight: 700; color: var(--slate-900);">
                                            <a href="/properties/view/<?= esc($pr['property_id']) ?>" style="color: inherit; text-decoration: none;">
                                                <?= esc($pr['property_title']) ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><code><?= esc($pr['property_code']) ?></code></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 700; color: var(--slate-900);">
                                    ₹<?= number_format((float)$pr['base_price'], 2) ?>
                                </td>
                                <td>
                                    <?= (float)$pr['price_per_sqft'] > 0 ? '₹' . number_format((float)$pr['price_per_sqft'], 2) : '—' ?>
                                </td>
                                <td>
                                    <?= (float)$pr['market_price'] > 0 ? '₹' . number_format((float)$pr['market_price'], 2) : '—' ?>
                                </td>
                                <td>
                                    <?= (float)$pr['negotiated_price'] > 0 ? '₹' . number_format((float)$pr['negotiated_price'], 2) : '—' ?>
                                </td>
                                <td>
                                    <?= (float)$pr['discount'] > 0 ? '₹' . number_format((float)$pr['discount'], 2) : '0.00' ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-600); white-space: nowrap;">
                                    <?= esc($pr['effective_from'] ?: 'Immediate') ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-600); max-width: 200px;">
                                    <?= esc($pr['remarks'] ?: '—') ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/properties/view/<?= esc($pr['property_id']) ?>" class="btn btn-sm btn-secondary">
                                            View
                                        </a>
                                        <?php if (session()->get('is_super_admin') || in_array('pricing.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/pricing/delete/<?= esc($pr['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this pricing record?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer" style="display: flex; justify-content: flex-end; padding: 1rem;">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
