<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Users</span>
        </div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">Manage system users, corporate roles, and account access</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('users.create', session()->get('permissions') ?? [])): ?>
            <a href="/users/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                Create New User
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/users" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="<?= esc($search) ?>">
        </div>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>

        <select name="role_id" class="form-control" style="width: auto;">
            <option value="0">All Roles</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= esc($r['id']) ?>" <?= (int)$roleId === (int)$r['id'] ? 'selected' : '' ?>>
                    <?= esc($r['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($status) || !empty($roleId)): ?>
            <a href="/users" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<!-- Users Table Card -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th>Assigned Roles</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>
                                    </div>
                                    <div class="empty-state-title">No Users Found</div>
                                    <div class="empty-state-desc">No registered user matches your search or filter parameters.</div>
                                    <a href="/users" class="btn btn-sm btn-secondary">Clear Filters</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--slate-900);"><?= esc($u['name']) ?></div>
                                    <div style="font-size: 0.78rem; color: var(--slate-500);"><?= esc($u['email']) ?></div>
                                </td>
                                <td><?= esc($u['phone'] ?: '—') ?></td>
                                <td><?= esc($u['branch_name'] ?? 'Headquarters') ?></td>
                                <td>
                                    <?php if (!empty($u['roles'])): ?>
                                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                            <?php foreach ($u['roles'] as $ur): ?>
                                                <span class="badge-role"><?= esc($ur['name']) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.8rem;">No Role</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $u['status'] ?>">
                                        <span class="badge-dot"></span>
                                        <?= ucfirst(esc($u['status'])) ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--slate-600); white-space: nowrap;">
                                    <?= $u['last_login_at'] ? esc(date('M d, Y h:i A', strtotime($u['last_login_at']))) : '<span style="color: var(--slate-400);">Never</span>' ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="/users/view/<?= esc($u['id']) ?>" class="btn-icon" title="View Profile">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>

                                    <?php if (session()->get('is_super_admin') || in_array('users.edit', session()->get('permissions') ?? [])): ?>
                                        <a href="/users/edit/<?= esc($u['id']) ?>" class="btn-icon" title="Edit User">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ((session()->get('is_super_admin') || in_array('users.delete', session()->get('permissions') ?? [])) && (int)$u['id'] !== (int)session()->get('user_id')): ?>
                                        <button type="button" class="btn-icon delete" title="Delete User" onclick="openConfirmModal('/users/delete/<?= esc($u['id']) ?>', 'Delete User Account', 'Are you sure you want to deactivate and remove <?= esc($u['name']) ?>?')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
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
                <div class="pagination-info">Showing active page results</div>
                <div class="pagination-links">
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
