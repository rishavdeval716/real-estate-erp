<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Availability History</span>
        </div>
        <h1 class="page-title">Property Availability Transitions</h1>
        <p class="page-subtitle">Audit trail of availability status movements across properties and project units</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Target Item</th>
                        <th>Type</th>
                        <th>Previous Status</th>
                        <th></th>
                        <th>New Status</th>
                        <th>Changed By</th>
                        <th>Remarks / Reason</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No status changes recorded yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--slate-500);">#<?= esc($h['id']) ?></td>
                                <td>
                                    <?php if ($h['unit_id']): ?>
                                        <div style="font-weight: 700; color: var(--primary);">
                                            <a href="/units/view/<?= esc($h['unit_id']) ?>" style="color: inherit; text-decoration: none;">
                                                Unit <?= esc($h['unit_number']) ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($h['property_title'] ?? 'Standalone Project Unit') ?></div>
                                    <?php else: ?>
                                        <div style="font-weight: 700; color: var(--slate-900);">
                                            <a href="/properties/view/<?= esc($h['property_id']) ?>" style="color: inherit; text-decoration: none;">
                                                <?= esc($h['property_title']) ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><code><?= esc($h['property_code']) ?></code></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                        <?= $h['unit_id'] ? 'Unit' : 'Property' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                        <?= esc($h['old_status']) ?>
                                    </span>
                                </td>
                                <td style="color: var(--slate-400);">&rarr;</td>
                                <td>
                                    <?= \App\Libraries\PropertyStatus::renderBadge($h['new_status']) ?>
                                </td>
                                <td style="font-weight: 600; color: var(--slate-800);">
                                    <?= esc($h['user_name'] ?? 'System') ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-600); max-width: 280px;">
                                    <?= esc($h['remarks'] ?: '—') ?>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--slate-500); white-space: nowrap;">
                                    <?= esc(date('M d, Y h:i A', strtotime($h['created_at']))) ?>
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
