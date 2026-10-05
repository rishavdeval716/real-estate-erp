<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/locations">Locations</a>
            <span class="separator">/</span>
            <span><?= esc($location['area']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($location['area']) ?>, <?= esc($location['city']) ?></h1>
        <p class="page-subtitle">Geographic location profile and associated projects & property developments</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('locations.edit', session()->get('permissions') ?? [])): ?>
            <a href="/locations/edit/<?= esc($location['id']) ?>" class="btn btn-primary">Edit Location</a>
        <?php endif; ?>
        <a href="/locations" class="btn btn-secondary">Back to Locations</a>
    </div>
</div>

<div class="form-row">
    <!-- Location Profile Card -->
    <div class="form-col-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Location Specifications</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Area & Locality</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--slate-900);"><?= esc($location['area']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--slate-500);"><?= esc($location['locality'] ?: '') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">City & State</div>
                    <div style="font-size: 0.95rem; font-weight: 600; color: var(--slate-800);"><?= esc($location['city']) ?>, <?= esc($location['state']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--slate-500);">Pincode: <code><?= esc($location['pincode']) ?></code></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Landmark</div>
                    <div style="font-size: 0.88rem; color: var(--slate-700);"><?= esc($location['landmark'] ?: 'None recorded') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Nearby Hubs</div>
                    <div style="font-size: 0.85rem; color: var(--slate-600);"><?= esc($location['nearby_locations'] ?: 'None recorded') ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Map Coordinate</div>
                    <div style="font-size: 0.85rem; color: var(--slate-600);"><?= esc($location['map_location'] ?: '—') ?></div>
                </div>

                <div>
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700; margin-bottom: 0.25rem;">Status</div>
                    <span class="badge badge-<?= $location['status'] ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($location['status'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Projects & Properties -->
    <div class="form-col-8">
        <!-- Projects Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Projects in this Location</h2>
                    <div class="card-subtitle">Master real estate developments situated here</div>
                </div>
                <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 700;">
                    <?= count($projects) ?> Projects
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Project Code</th>
                                <th>Project Name</th>
                                <th>Builder</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 2rem;">
                                        No projects registered in this location yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $proj): ?>
                                    <tr>
                                        <td><code><?= esc($proj['project_code']) ?></code></td>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($proj['name']) ?></td>
                                        <td><?= esc($proj['builder_developer']) ?></td>
                                        <td>
                                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 600;">
                                                <?= esc($proj['construction_status']) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/projects/view/<?= esc($proj['id']) ?>" class="btn btn-sm btn-secondary">View Project</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Properties Card -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Individual Properties Listed</h2>
                    <div class="card-subtitle">Available and managed property listings in this zone</div>
                </div>
                <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 700;">
                    <?= count($properties) ?> Properties
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Property Title</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 2rem;">
                                        No individual properties currently listed here.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($properties as $prop): ?>
                                    <tr>
                                        <td><code><?= esc($prop['property_code']) ?></code></td>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($prop['title']) ?></td>
                                        <td style="font-weight: 700; color: var(--primary);">₹<?= number_format((float)$prop['price'], 2) ?></td>
                                        <td>
                                            <?= \App\Libraries\PropertyStatus::renderBadge($prop['status']) ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/properties/view/<?= esc($prop['id']) ?>" class="btn btn-sm btn-secondary">View Property</a>
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
