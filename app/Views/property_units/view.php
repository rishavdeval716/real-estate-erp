<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/units">Property Units</a>
            <span class="separator">/</span>
            <span>Unit <?= esc($unit['unit_number']) ?></span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
            <h1 class="page-title" style="margin: 0;">Unit <?= esc($unit['unit_number']) ?></h1>
            <?= \App\Libraries\PropertyStatus::renderBadge($unit['availability_status']) ?>
        </div>
        <p class="page-subtitle" style="margin-top: 0.25rem;">
            <?= esc($unit['flat_type']) ?> &bull; <?= esc($unit['project_name']) ?> &bull; <?= esc($unit['tower_name'] ?: 'Standalone') ?>
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <?php if (session()->get('is_super_admin') || in_array('availability.edit', session()->get('permissions') ?? [])): ?>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalChangeUnitStatus').style.display='block'">
                Change Availability
            </button>
        <?php endif; ?>
        <?php if (session()->get('is_super_admin') || in_array('units.edit', session()->get('permissions') ?? [])): ?>
            <a href="/units/edit/<?= esc($unit['id']) ?>" class="btn btn-primary">Edit Unit</a>
        <?php endif; ?>
    </div>
</div>

<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Unit Price</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format((float)$unit['unit_price'], 2) ?></div>
            <div class="metric-meta">
                <span>₹<?= (float)$unit['built_up_area'] > 0 ? number_format((float)$unit['unit_price'] / (float)$unit['built_up_area'], 2) : 0 ?> / sq.ft</span>
            </div>
        </div>
    </div>
    <div class="metric-card info">
        <div>
            <div class="metric-label">Built-up Area</div>
            <div class="metric-value"><?= number_format((float)$unit['built_up_area'], 2) ?></div>
            <div class="metric-meta"><span>Carpet: <?= number_format((float)$unit['carpet_area'], 2) ?> sq.ft</span></div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Floor Location</div>
            <div class="metric-value">Floor <?= esc($unit['floor']) ?></div>
            <div class="metric-meta"><span>Facing: <?= esc($unit['facing'] ?: 'Not specified') ?></span></div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Parking & Balcony</div>
            <div class="metric-value"><?= esc($unit['parking']) ?>P / <?= esc($unit['balcony']) ?>B</div>
            <div class="metric-meta"><span>Dedicated spaces</span></div>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="form-col-5">
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h2 class="card-title">Unit Specifications</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Unit Identification</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--slate-900);"><?= esc($unit['unit_number']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--slate-500);"><?= esc($unit['flat_type']) ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Project & Tower</div>
                    <div style="font-weight: 600; color: var(--slate-900);">
                        <a href="/projects/view/<?= esc($unit['project_id']) ?>" style="color: var(--primary); text-decoration: none;">
                            <?= esc($unit['project_name']) ?>
                        </a>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--slate-600);"><?= esc($unit['tower_name'] ?: 'Standalone Block') ?> &bull; Floor <?= esc($unit['floor']) ?></div>
                </div>

                <?php if (!empty($unit['property_id'])): ?>
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Associated Property Listing</div>
                        <div style="font-weight: 600;">
                            <a href="/properties/view/<?= esc($unit['property_id']) ?>" style="color: var(--primary); text-decoration: none;">
                                <?= esc($unit['property_title']) ?> (<?= esc($unit['property_code_ref']) ?>)
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Balcony & Parking</div>
                    <div style="font-size: 0.9rem; color: var(--slate-800);">
                        Balconies: <strong><?= esc($unit['balcony']) ?></strong> &bull;
                        Parking Slots: <strong><?= esc($unit['parking']) ?></strong>
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 0.25rem;">Availability Status</div>
                    <?= \App\Libraries\PropertyStatus::renderBadge($unit['availability_status']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status History -->
    <div class="form-col-7">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Availability Status History</h2>
                    <div class="card-subtitle">Transitions logged for this unit</div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Changed By</th>
                                <th>Remarks</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($statusHistory)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                        No status history transitions logged yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($statusHistory as $sh): ?>
                                    <tr>
                                        <td>
                                            <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                                <?= esc($sh['old_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= \App\Libraries\PropertyStatus::renderBadge($sh['new_status']) ?></td>
                                        <td style="font-weight: 600;"><?= esc($sh['user_name'] ?? 'System') ?></td>
                                        <td style="font-size: 0.85rem; color: var(--slate-600);"><?= esc($sh['remarks'] ?: '—') ?></td>
                                        <td style="font-size: 0.8rem; color: var(--slate-500); white-space: nowrap;">
                                            <?= esc(date('M d, Y h:i A', strtotime($sh['created_at']))) ?>
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
</div>

<!-- Modal: Change Unit Status -->
<div id="modalChangeUnitStatus" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; max-width: 500px; width: 90%; margin: 10% auto; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--slate-200); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Update Unit Availability</h3>
            <button type="button" onclick="document.getElementById('modalChangeUnitStatus').style.display='none'" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form action="/availability/unit" method="POST" style="padding: 1.25rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="unit_id" value="<?= esc($unit['id']) ?>">

            <div class="form-group">
                <label class="form-label">Current Status</label>
                <div><?= \App\Libraries\PropertyStatus::renderBadge($unit['availability_status']) ?></div>
            </div>

            <div class="form-group">
                <label for="unit_new_status" class="form-label required">Select New Status</label>
                <select name="status" id="unit_new_status" class="form-control" required>
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= esc($st) ?>" <?= $unit['availability_status'] === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="unit_status_remarks" class="form-label">Remarks / Operational Note</label>
                <textarea name="remarks" id="unit_status_remarks" rows="3" class="form-control" placeholder="Specify reason for unit availability status change..."></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalChangeUnitStatus').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
