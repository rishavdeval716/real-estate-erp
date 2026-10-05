<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Locations</span>
        </div>
        <h1 class="page-title">Location Management</h1>
        <p class="page-subtitle">Manage geographic areas, localities, landmarks, and postal hubs reusable across properties and projects</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('locations.create', session()->get('permissions') ?? [])): ?>
            <a href="/locations/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Location
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/locations" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search area, landmark, pincode, city..." value="<?= esc($search) ?>">
        </div>

        <select name="city" class="form-control" style="width: auto;">
            <option value="">All Cities</option>
            <?php foreach ($cities as $c): ?>
                <option value="<?= esc($c) ?>" <?= $city === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($city) || !empty($status)): ?>
            <a href="/locations" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Area / Neighborhood</th>
                        <th>City & State</th>
                        <th>Locality / Landmark</th>
                        <th>Pincode</th>
                        <th>Projects</th>
                        <th>Properties</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($locations)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No locations found matching your search.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($locations as $loc): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);">
                                        <a href="/locations/view/<?= esc($loc['id']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($loc['area']) ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($loc['nearby_locations'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);">Near: <?= esc($loc['nearby_locations']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= esc($loc['city']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($loc['state']) ?></div>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-600);">
                                    <?= esc($loc['landmark'] ?: ($loc['locality'] ?: '—')) ?>
                                </td>
                                <td>
                                    <code style="background: var(--slate-100); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.82rem; color: var(--slate-700);">
                                        <?= esc($loc['pincode']) ?>
                                    </code>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                        <?= esc($loc['project_count'] ?? 0) ?> Projects
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                        <?= esc($loc['property_count'] ?? 0) ?> Properties
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $loc['status'] ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($loc['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/locations/view/<?= esc($loc['id']) ?>" class="btn btn-sm btn-secondary">View</a>
                                        <?php if (session()->get('is_super_admin') || in_array('locations.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/locations/edit/<?= esc($loc['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('locations.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/locations/delete/<?= esc($loc['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this location?');">
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
