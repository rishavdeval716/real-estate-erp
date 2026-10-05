<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/roles">Roles</a>
            <span class="separator">/</span>
            <span>View</span>
        </div>
        <h1 class="page-title">Role: <?= esc($role['name']) ?></h1>
        <p class="page-subtitle"><?= esc($role['description'] ?: 'Configured system security role') ?></p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('roles.edit', session()->get('permissions') ?? [])): ?>
            <a href="/roles/edit/<?= esc($role['id']) ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Edit Role
            </a>
        <?php endif; ?>
        <a href="/roles" class="btn btn-secondary">Back to Roles</a>
    </div>
</div>

<div class="form-row">
    <!-- Permissions list -->
    <div class="form-col-6">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Granted Permissions</h2>
                    <div class="card-subtitle"><?= count($permissions) ?> active capabilities assigned</div>
                </div>
            </div>
            <div class="card-body">
                <?php if ($role['name'] === 'Super Admin'): ?>
                    <div class="alert alert-info">
                        <strong>Unrestricted Access:</strong> The Super Admin role bypasses granular permission restrictions and inherently holds all current and future capabilities.
                    </div>
                <?php endif; ?>

                <?php if (empty($permissions) && $role['name'] !== 'Super Admin'): ?>
                    <div style="color: var(--slate-400); padding: 1.5rem; text-align: center;">No specific permissions assigned to this role yet.</div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($permissions as $p): ?>
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.85rem; background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-sm);">
                                <div>
                                    <div style="font-weight: 600; color: var(--slate-800); font-size: 0.88rem;"><?= esc($p['name']) ?></div>
                                    <div style="font-size: 0.72rem; color: var(--slate-400); font-family: monospace;"><?= esc($p['slug']) ?></div>
                                </div>
                                <span class="badge badge-role" style="font-size: 0.72rem;"><?= esc($p['group_name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Assigned Users -->
    <div class="form-col-6">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Users With This Role</h2>
                    <div class="card-subtitle"><?= count($users) ?> accounts assigned</div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">No users currently assigned this role.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($u['name']) ?></div>
                                            <div style="font-size: 0.76rem; color: var(--slate-500);"><?= esc($u['email']) ?></div>
                                        </td>
                                        <td><?= esc($u['branch_name'] ?? 'Headquarters') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $u['status'] ?>">
                                                <span class="badge-dot"></span>
                                                <?= ucfirst(esc($u['status'])) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/users/view/<?= esc($u['id']) ?>" class="btn-icon" title="View Profile">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
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
