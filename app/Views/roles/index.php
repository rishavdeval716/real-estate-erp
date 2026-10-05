<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Roles</span>
        </div>
        <h1 class="page-title">Role Management</h1>
        <p class="page-subtitle">Security roles, operational tiers, and assigned permissions</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('roles.create', session()->get('permissions') ?? [])): ?>
            <a href="/roles/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Role
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Assigned Users</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900); font-size: 0.95rem;"><?= esc($role['name']) ?></div>
                                <?php if ($role['name'] === 'Super Admin'): ?>
                                    <span style="font-size: 0.72rem; color: #3b82f6; font-weight: 700;">FULL SYSTEM PRIVILEGES</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--slate-600); max-width: 320px;"><?= esc($role['description'] ?: 'No description provided') ?></td>
                            <td>
                                <span class="badge badge-role" style="background: var(--slate-100); color: var(--slate-700); border-color: var(--slate-200);">
                                    <?= esc($role['user_count']) ?> Users
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-role">
                                    <?= $role['name'] === 'Super Admin' ? 'All (Unrestricted)' : esc($role['permission_count']) . ' Active' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $role['status'] ?>">
                                    <span class="badge-dot"></span>
                                    <?= ucfirst(esc($role['status'])) ?>
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="/roles/view/<?= esc($role['id']) ?>" class="btn-icon" title="View Role Details">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>

                                <?php if (session()->get('is_super_admin') || in_array('roles.edit', session()->get('permissions') ?? [])): ?>
                                    <a href="/roles/edit/<?= esc($role['id']) ?>" class="btn-icon" title="Edit Role & Permissions">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </a>
                                <?php endif; ?>

                                <?php if ($role['name'] !== 'Super Admin' && (session()->get('is_super_admin') || in_array('roles.delete', session()->get('permissions') ?? []))): ?>
                                    <button type="button" class="btn-icon delete" title="Delete Role" onclick="openConfirmModal('/roles/delete/<?= esc($role['id']) ?>', 'Delete Role', 'Are you sure you want to permanently delete role \'<?= esc($role['name']) ?>\'?')">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
