<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Property Types</span>
        </div>
        <h1 class="page-title">Property Types</h1>
        <p class="page-subtitle">Configure real estate classifications (Residential, Commercial, Industrial, Plots, Villas, etc.)</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('property_types.create', session()->get('permissions') ?? [])): ?>
            <a href="/property-types/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Property Type
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/property-types" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="<?= esc($search) ?>">
        </div>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($status)): ?>
            <a href="/property-types" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
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
                        <th>Type Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Properties</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($types)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No property types found matching your query.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($types as $t): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--slate-500);">#<?= esc($t['id']) ?></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);">
                                        <a href="/property-types/view/<?= esc($t['id']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($t['name']) ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <code style="background: var(--slate-100); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.8rem; color: var(--slate-700);">
                                        <?= esc($t['slug']) ?>
                                    </code>
                                </td>
                                <td style="color: var(--slate-600); max-width: 280px; font-size: 0.85rem;">
                                    <?= esc($t['description'] ?: '—') ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 600;">
                                        <?= esc($t['property_count'] ?? 0) ?> Listings
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $t['status'] ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($t['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/property-types/view/<?= esc($t['id']) ?>" class="btn btn-sm btn-secondary" title="View Details">
                                            View
                                        </a>
                                        <?php if (session()->get('is_super_admin') || in_array('property_types.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/property-types/edit/<?= esc($t['id']) ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('property_types.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/property-types/delete/<?= esc($t['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this property type?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">
                                                    Delete
                                                </button>
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
