<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/projects">Projects</a>
            <span class="separator">/</span>
            <span><?= esc($project['name']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($project['name']) ?></h1>
        <p class="page-subtitle">Master real estate development portfolio, towers, and inventory control</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('projects.edit', session()->get('permissions') ?? [])): ?>
            <a href="/projects/edit/<?= esc($project['id']) ?>" class="btn btn-secondary">Edit Project</a>
        <?php endif; ?>
        <?php if (session()->get('is_super_admin') || in_array('units.create', session()->get('permissions') ?? [])): ?>
            <a href="/units/create?project_id=<?= esc($project['id']) ?>" class="btn btn-primary">Add Unit</a>
        <?php endif; ?>
    </div>
</div>

<!-- Dynamic Inventory Summary Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Units</div>
            <div class="metric-value"><?= number_format($inventory['total']) ?></div>
            <div class="metric-meta"><span>Registered project units</span></div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Available Units</div>
            <div class="metric-value"><?= number_format($inventory['available']) ?></div>
            <div class="metric-meta" style="color: var(--success);"><span>Ready to sell/lease</span></div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Reserved Units</div>
            <div class="metric-value"><?= number_format($inventory['reserved']) ?></div>
            <div class="metric-meta"><span>Client holds</span></div>
        </div>
    </div>
    <div class="metric-card info">
        <div>
            <div class="metric-label">Booked Units</div>
            <div class="metric-value"><?= number_format($inventory['booked']) ?></div>
            <div class="metric-meta"><span>Token / Booking made</span></div>
        </div>
    </div>
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Sold Units</div>
            <div class="metric-value"><?= number_format($inventory['sold']) ?></div>
            <div class="metric-meta"><span>Completed sales</span></div>
        </div>
    </div>
    <div class="metric-card" style="border-left: 4px solid var(--slate-700);">
        <div>
            <div class="metric-label">Rented Units</div>
            <div class="metric-value"><?= number_format($inventory['rented']) ?></div>
            <div class="metric-meta"><span>Leased out</span></div>
        </div>
    </div>
</div>

<!-- Project Details & Towers Section -->
<div class="form-row">
    <!-- Project Master Information -->
    <div class="form-col-4">
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h2 class="card-title">Project Specifications</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Project Code</div>
                    <code><?= esc($project['project_code']) ?></code>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Builder / Developer</div>
                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($project['builder_developer']) ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Location</div>
                    <div style="font-weight: 600; color: var(--slate-800);"><?= esc($project['area']) ?>, <?= esc($project['city']) ?></div>
                    <div style="font-size: 0.82rem; color: var(--slate-500);"><?= esc($project['landmark'] ?: ($project['locality'] ?: '')) ?> (<?= esc($project['pincode']) ?>)</div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Construction Status</div>
                    <span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 600; font-size: 0.85rem;">
                        <?= esc($project['construction_status']) ?>
                    </span>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Possession Date</div>
                    <div style="font-weight: 600; color: var(--slate-800);">
                        <?= $project['possession_date'] ? esc(date('F Y', strtotime($project['possession_date']))) : 'Immediate' ?>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Description</div>
                    <div style="font-size: 0.88rem; color: var(--slate-600);"><?= esc($project['description'] ?: 'No description entered.') ?></div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 0.25rem;">Status</div>
                    <span class="badge badge-<?= $project['status'] === 'active' ? 'active' : 'inactive' ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($project['status'])) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Add Tower Card -->
        <?php if (session()->get('is_super_admin') || in_array('projects.edit', session()->get('permissions') ?? [])): ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Add Tower / Block</h2>
            </div>
            <div class="card-body">
                <form action="/projects/towers/store/<?= esc($project['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="tower_name" class="form-label required">Tower Name</label>
                        <input type="text" name="tower_name" id="tower_name" class="form-control" placeholder="e.g. Tower C (Park View)" required>
                    </div>
                    <div class="form-group">
                        <label for="tower_code" class="form-label required">Tower Code</label>
                        <input type="text" name="tower_code" id="tower_code" class="form-control" placeholder="e.g. TWR-C" required>
                    </div>
                    <div class="form-row">
                        <div class="form-col-6">
                            <div class="form-group">
                                <label for="number_of_floors" class="form-label required">Floors</label>
                                <input type="number" name="number_of_floors" id="number_of_floors" class="form-control" value="10" min="1" required>
                            </div>
                        </div>
                        <div class="form-col-6">
                            <div class="form-group">
                                <label for="tower_status" class="form-label required">Status</label>
                                <select name="status" id="tower_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Add Tower</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Towers & Inventory Tables -->
    <div class="form-col-8">
        <!-- Towers List -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Towers / Blocks</h2>
                    <div class="card-subtitle">Configured towers and wings in this project</div>
                </div>
                <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 700;">
                    <?= count($towers) ?> Towers
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tower Code</th>
                                <th>Tower Name</th>
                                <th>Floors</th>
                                <th>Units Created</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($towers)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 2rem;">
                                        No towers configured yet. Use the form on the left to add a tower.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($towers as $twr): ?>
                                    <tr>
                                        <td><code><?= esc($twr['tower_code']) ?></code></td>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($twr['tower_name']) ?></td>
                                        <td><?= esc($twr['number_of_floors']) ?> Floors</td>
                                        <td>
                                            <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 600;">
                                                <?= esc($twr['actual_units'] ?? 0) ?> Units
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $twr['status'] ?>">
                                                <span class="badge-dot"></span>
                                                <?= ucfirst(esc($twr['status'])) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/inventory?project_id=<?= esc($project['id']) ?>&tower_id=<?= esc($twr['id']) ?>" class="btn btn-sm btn-secondary">
                                                Matrix
                                            </a>
                                            <?php if (session()->get('is_super_admin') || in_array('projects.edit', session()->get('permissions') ?? [])): ?>
                                                <form action="/projects/towers/delete/<?= esc($twr['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Remove tower <?= esc($twr['tower_name']) ?>?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">Delete</button>
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

        <!-- Registered Units in Project -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Project Unit Inventory</h2>
                    <div class="card-subtitle">Registered units, flats, and spaces</div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/inventory?project_id=<?= esc($project['id']) ?>" class="btn btn-sm btn-secondary">Full Visual Matrix</a>
                    <a href="/units/create?project_id=<?= esc($project['id']) ?>" class="btn btn-sm btn-primary">+ Add Unit</a>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Unit #</th>
                                <th>Tower</th>
                                <th>Floor</th>
                                <th>Type</th>
                                <th>Built-up Area</th>
                                <th>Price</th>
                                <th>Availability</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($units)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                        No units created for this project yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($units as $u): ?>
                                    <tr>
                                        <td>
                                            <a href="/units/view/<?= esc($u['id']) ?>" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                                <?= esc($u['unit_number']) ?>
                                            </a>
                                        </td>
                                        <td><?= esc($u['tower_name'] ?: 'Independent') ?></td>
                                        <td>Floor <?= esc($u['floor']) ?></td>
                                        <td><?= esc($u['flat_type']) ?></td>
                                        <td><?= number_format((float)$u['built_up_area'], 2) ?> sq.ft</td>
                                        <td style="font-weight: 700; color: var(--slate-900);">₹<?= number_format((float)$u['unit_price'], 2) ?></td>
                                        <td>
                                            <?= \App\Libraries\PropertyStatus::renderBadge($u['availability_status']) ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/units/view/<?= esc($u['id']) ?>" class="btn btn-sm btn-secondary">View</a>
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
<?= $this->endSection() ?>
