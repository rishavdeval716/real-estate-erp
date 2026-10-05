<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Unit Inventory Matrix</span>
        </div>
        <h1 class="page-title">Project Unit Inventory Matrix</h1>
        <p class="page-subtitle">Real-time floor-wise and tower-wise inventory status calculated dynamically from MySQL</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <?php if ($selectedProject): ?>
            <a href="/units/create?project_id=<?= esc($selectedProject['id']) ?>" class="btn btn-primary">
                + Add Unit to Project
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Project & Tower Selector Filter -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="padding: 1rem 1.25rem;">
        <form method="GET" action="/inventory" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 250px;">
                <label class="form-label" style="font-size: 0.82rem; margin-bottom: 0.35rem;">Select Project</label>
                <select name="project_id" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($projects as $p): ?>
                        <option value="<?= esc($p['id']) ?>" <?= $projectId == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['name']) ?> (<?= esc($p['project_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($towers)): ?>
                <div style="width: 220px;">
                    <label class="form-label" style="font-size: 0.82rem; margin-bottom: 0.35rem;">Filter by Tower</label>
                    <select name="tower_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Towers & Wings</option>
                        <?php foreach ($towers as $twr): ?>
                            <option value="<?= esc($twr['id']) ?>" <?= $towerId == $twr['id'] ? 'selected' : '' ?>>
                                <?= esc($twr['tower_name']) ?> (<?= esc($twr['tower_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <a href="/inventory?project_id=<?= esc($projectId) ?>" class="btn btn-secondary">Reset Tower</a>
            </div>
        </form>
    </div>
</div>

<?php if ($selectedProject): ?>
    <!-- Live Inventory Status Counters -->
    <div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom: 1.5rem;">
        <div class="metric-card primary">
            <div>
                <div class="metric-label">Total Units</div>
                <div class="metric-value"><?= number_format($inventory['total']) ?></div>
                <div class="metric-meta"><span>100% capacity</span></div>
            </div>
        </div>
        <div class="metric-card success">
            <div>
                <div class="metric-label">Available</div>
                <div class="metric-value"><?= number_format($inventory['available']) ?></div>
                <div class="metric-meta" style="color: var(--success);"><span>Ready to sell</span></div>
            </div>
        </div>
        <div class="metric-card warning">
            <div>
                <div class="metric-label">Reserved</div>
                <div class="metric-value"><?= number_format($inventory['reserved']) ?></div>
                <div class="metric-meta"><span>Client holds</span></div>
            </div>
        </div>
        <div class="metric-card info">
            <div>
                <div class="metric-label">Under Negotiation</div>
                <div class="metric-value"><?= number_format($inventory['under_negotiation']) ?></div>
                <div class="metric-meta"><span>Active bids</span></div>
            </div>
        </div>
        <div class="metric-card info">
            <div>
                <div class="metric-label">Booked</div>
                <div class="metric-value"><?= number_format($inventory['booked']) ?></div>
                <div class="metric-meta"><span>Tokens secured</span></div>
            </div>
        </div>
        <div class="metric-card secondary">
            <div>
                <div class="metric-label">Sold</div>
                <div class="metric-value"><?= number_format($inventory['sold']) ?></div>
                <div class="metric-meta"><span>Deals closed</span></div>
            </div>
        </div>
        <div class="metric-card" style="border-left: 4px solid var(--slate-700);">
            <div>
                <div class="metric-label">Rented</div>
                <div class="metric-value"><?= number_format($inventory['rented']) ?></div>
                <div class="metric-meta"><span>Active leases</span></div>
            </div>
        </div>
    </div>

    <!-- Floor-wise Inventory Matrix -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title">Floor-by-Floor Unit Matrix</h2>
                <div class="card-subtitle"><?= esc($selectedProject['name']) ?> &bull; <?= esc($selectedProject['builder_developer']) ?></div>
            </div>
            <div style="font-size: 0.82rem; color: var(--slate-500);">
                Click on any unit card to view complete specifications or change status.
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($floorWiseUnits)): ?>
                <div style="text-align: center; color: var(--slate-400); padding: 4rem;">
                    No property units found for this selection.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($floorWiseUnits as $floorNum => $unitsOnFloor): ?>
                        <div style="background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: 8px; padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                                <div style="font-weight: 700; color: var(--slate-900); font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <span><?= $floorNum == 0 ? 'Ground Floor (G)' : 'Floor ' . esc($floorNum) ?></span>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--slate-500);">(<?= count($unitsOnFloor) ?> Units)</span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 0.85rem;">
                                <?php foreach ($unitsOnFloor as $u): ?>
                                    <a href="/units/view/<?= esc($u['id']) ?>" style="text-decoration: none; color: inherit;">
                                        <div class="card" style="margin: 0; padding: 0.85rem; border: 1px solid var(--slate-200); transition: all 0.2s ease; cursor: pointer; background: #fff;" onmouseover="this.style.borderColor='var(--primary)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='var(--slate-200)'; this.style.transform='none';">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.4rem;">
                                                <div style="font-weight: 700; color: var(--slate-900); font-size: 1rem;">
                                                    <?= esc($u['unit_number']) ?>
                                                </div>
                                                <?= \App\Libraries\PropertyStatus::renderBadge($u['availability_status']) ?>
                                            </div>

                                            <div style="font-size: 0.82rem; font-weight: 600; color: var(--slate-700);">
                                                <?= esc($u['flat_type']) ?>
                                            </div>

                                            <div style="font-size: 0.78rem; color: var(--slate-500); margin-top: 0.2rem;">
                                                <?= number_format((float)$u['built_up_area'], 2) ?> sq.ft &bull; <?= esc($u['facing'] ?: 'Standard') ?>
                                            </div>

                                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem; border-top: 1px dashed var(--slate-200); padding-top: 0.35rem;">
                                                ₹<?= number_format((float)$u['unit_price'], 2) ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
