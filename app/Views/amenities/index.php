<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Amenities</span>
        </div>
        <h1 class="page-title">Property Amenities</h1>
        <p class="page-subtitle">Configure amenities and lifestyle facilities (Gym, Swimming Pool, Parking, Security, etc.)</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('amenities.create', session()->get('permissions') ?? [])): ?>
            <a href="/amenities/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Amenity
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/amenities" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search amenity name or description..." value="<?= esc($search) ?>">
        </div>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($status)): ?>
            <a href="/amenities" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amenity Name</th>
                        <th>Icon Class / Tag</th>
                        <th>Description</th>
                        <th>Linked Properties</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($amenities)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No amenities found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($amenities as $am): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--slate-500);">#<?= esc($am['id']) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);">
                                        <?= esc($am['name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <code style="background: var(--slate-100); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.8rem; color: var(--slate-700);">
                                        <?= esc($am['icon'] ?: 'ri-checkbox-circle-line') ?>
                                    </code>
                                </td>
                                <td style="color: var(--slate-600); font-size: 0.85rem; max-width: 320px;">
                                    <?= esc($am['description'] ?: '—') ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 600;">
                                        <?= esc($am['usage_count'] ?? 0) ?> Properties
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $am['status'] ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($am['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <?php if (session()->get('is_super_admin') || in_array('amenities.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/amenities/edit/<?= esc($am['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('amenities.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/amenities/delete/<?= esc($am['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this amenity?');">
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
