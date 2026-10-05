<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Permissions</span>
        </div>
        <h1 class="page-title">Permission Management</h1>
        <p class="page-subtitle">Configure granular capability keys accessible by security roles</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('permissions.create', session()->get('permissions') ?? [])): ?>
            <a href="/permissions/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Permission
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Group Filter -->
<form method="GET" action="/permissions" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search name, slug, description..." value="<?= esc($search) ?>">
        </div>

        <select name="group" class="form-control" style="width: auto;">
            <option value="">All Groups</option>
            <?php foreach ($groups as $g): ?>
                <option value="<?= esc($g) ?>" <?= $selectedGroup === $g ? 'selected' : '' ?>>
                    <?= esc($g) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($selectedGroup)): ?>
            <a href="/permissions" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Permission Name</th>
                        <th>Slug / Key</th>
                        <th>Module Group</th>
                        <th>Description</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permissions)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-title">No Permissions Found</div>
                                    <div class="empty-state-desc">Try modifying your search or filter options.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($permissions as $p): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--slate-900);"><?= esc($p['name']) ?></td>
                                <td>
                                    <code style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px; color: #0284c7; font-size: 0.82rem; border: 1px solid #e2e8f0;">
                                        <?= esc($p['slug']) ?>
                                    </code>
                                </td>
                                <td>
                                    <span class="badge badge-role"><?= esc($p['group_name']) ?></span>
                                </td>
                                <td style="color: var(--slate-600);"><?= esc($p['description'] ?: '—') ?></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <?php if (session()->get('is_super_admin') || in_array('permissions.edit', session()->get('permissions') ?? [])): ?>
                                        <a href="/permissions/edit/<?= esc($p['id']) ?>" class="btn-icon" title="Edit Permission">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (session()->get('is_super_admin') || in_array('permissions.delete', session()->get('permissions') ?? [])): ?>
                                        <button type="button" class="btn-icon delete" title="Delete Permission" onclick="openConfirmModal('/permissions/delete/<?= esc($p['id']) ?>', 'Delete Permission', 'Are you sure you want to remove capability \'<?= esc($p['name']) ?>\'?')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager): ?>
        <div class="card-footer">
            <div class="pagination-wrapper">
                <div class="pagination-info">Showing active permissions</div>
                <div class="pagination-links"><?= $pager->links() ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
