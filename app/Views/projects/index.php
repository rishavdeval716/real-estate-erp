<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Projects</span>
        </div>
        <h1 class="page-title">Project Management</h1>
        <p class="page-subtitle">Track residential and commercial developments, construction phases, towers, and inventory availability</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('projects.create', session()->get('permissions') ?? [])): ?>
            <a href="/projects/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Project
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/projects" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search project name, code, developer, city..." value="<?= esc($search) ?>">
        </div>

        <select name="location_id" class="form-control" style="width: auto;">
            <option value="">All Locations</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= esc($loc['id']) ?>" <?= $locationId == $loc['id'] ? 'selected' : '' ?>>
                    <?= esc($loc['city']) ?> - <?= esc($loc['area']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="construction_status" class="form-control" style="width: auto;">
            <option value="">All Construction Statuses</option>
            <option value="Pre-Launch" <?= $constructionStatus === 'Pre-Launch' ? 'selected' : '' ?>>Pre-Launch</option>
            <option value="Under Construction" <?= $constructionStatus === 'Under Construction' ? 'selected' : '' ?>>Under Construction</option>
            <option value="Ready to Move" <?= $constructionStatus === 'Ready to Move' ? 'selected' : '' ?>>Ready to Move</option>
            <option value="Completed" <?= $constructionStatus === 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option value="On Hold" <?= $constructionStatus === 'On Hold' ? 'selected' : '' ?>>On Hold</option>
        </select>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">Operational Status</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="archived" <?= $status === 'archived' ? 'selected' : '' ?>>Archived</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($locationId) || !empty($constructionStatus) || !empty($status)): ?>
            <a href="/projects" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Developer</th>
                        <th>Location</th>
                        <th>Construction Stage</th>
                        <th>Possession</th>
                        <th>Towers</th>
                        <th>Inventory (Avail/Total)</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No projects found matching your criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--slate-900);">
                                        <a href="/projects/view/<?= esc($p['id']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($p['name']) ?>
                                        </a>
                                    </div>
                                    <div><code style="font-size: 0.78rem;"><?= esc($p['project_code']) ?></code></div>
                                </td>
                                <td style="color: var(--slate-700); font-size: 0.88rem;">
                                    <?= esc($p['builder_developer']) ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-800);"><?= esc($p['area'] ?? '—') ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($p['city'] ?? '') ?>, <?= esc($p['state'] ?? '') ?></div>
                                </td>
                                <td>
                                    <span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 600;">
                                        <?= esc($p['construction_status']) ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-600); white-space: nowrap;">
                                    <?= $p['possession_date'] ? esc(date('M Y', strtotime($p['possession_date']))) : 'Immediate' ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 600;">
                                        <?= esc($p['tower_count'] ?? 0) ?> Towers
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 700;">
                                        <span style="color: var(--success);"><?= esc($p['dynamic_available_units']) ?></span>
                                        <span style="color: var(--slate-400);">/</span>
                                        <span style="color: var(--slate-800);"><?= esc($p['dynamic_total_units']) ?> Units</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $p['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($p['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/projects/view/<?= esc($p['id']) ?>" class="btn btn-sm btn-secondary">View</a>
                                        <?php if (session()->get('is_super_admin') || in_array('projects.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/projects/edit/<?= esc($p['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('projects.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/projects/delete/<?= esc($p['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this project?');">
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
