<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Branches</span>
        </div>
        <h1 class="page-title">Branch Management</h1>
        <p class="page-subtitle">Configure regional office branches, locations, and office managers</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('branches.create', session()->get('permissions') ?? [])): ?>
            <a href="/branches/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Branch
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/branches" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search branch, code, city, manager..." value="<?= esc($search) ?>">
        </div>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($status)): ?>
            <a href="/branches" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Branch Name</th>
                        <th>Code</th>
                        <th>City / Region</th>
                        <th>Branch Manager</th>
                        <th>Phone / Email</th>
                        <th>Personnel</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($branches)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-title">No Branches Found</div>
                                    <div class="empty-state-desc">Try modifying your search or filter options.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($branches as $b): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--slate-900);"><?= esc($b['name']) ?></div>
                                    <div style="font-size: 0.74rem; color: var(--slate-400);"><?= esc($b['company_name'] ?? 'Apex Horizon') ?></div>
                                </td>
                                <td><code><?= esc($b['code']) ?></code></td>
                                <td><?= esc($b['city'] ?: '—') ?><?= !empty($b['state']) ? ', ' . esc($b['state']) : '' ?></td>
                                <td><?= esc($b['manager_name'] ?: 'Not assigned') ?></td>
                                <td>
                                    <div style="font-size: 0.85rem; color: var(--slate-800);"><?= esc($b['phone'] ?: '—') ?></div>
                                    <div style="font-size: 0.74rem; color: var(--slate-500);"><?= esc($b['email'] ?: '') ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-role"><?= esc($b['user_count']) ?> Users</span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $b['status'] ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($b['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="/branches/view/<?= esc($b['id']) ?>" class="btn-icon" title="View Branch">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>

                                    <?php if (session()->get('is_super_admin') || in_array('branches.edit', session()->get('permissions') ?? [])): ?>
                                        <a href="/branches/edit/<?= esc($b['id']) ?>" class="btn-icon" title="Edit Branch">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (session()->get('is_super_admin') || in_array('branches.delete', session()->get('permissions') ?? [])): ?>
                                        <button type="button" class="btn-icon delete" title="Delete Branch" onclick="openConfirmModal('/branches/delete/<?= esc($b['id']) ?>', 'Delete Branch', 'Are you sure you want to deactivate and remove <?= esc($b['name']) ?>?')">
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
                <div class="pagination-info">Showing active branches</div>
                <div class="pagination-links"><?= $pager->links() ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
