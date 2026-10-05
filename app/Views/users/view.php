<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/users">Users</a>
            <span class="separator">/</span>
            <span>Profile</span>
        </div>
        <h1 class="page-title"><?= esc($user['name']) ?></h1>
        <p class="page-subtitle">User details, branch placement, and activity trail</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('users.edit', session()->get('permissions') ?? [])): ?>
            <a href="/users/edit/<?= esc($user['id']) ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Edit User
            </a>
        <?php endif; ?>
        <a href="/users" class="btn btn-secondary">Back to Users</a>
    </div>
</div>

<div class="form-row">
    <!-- User Overview Card -->
    <div class="form-col-4">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2.5rem 1.5rem;">
                <div class="user-avatar-sm" style="width: 72px; height: 72px; font-size: 1.8rem; margin: 0 auto 1.25rem;">
                    <?= strtoupper(substr(esc($user['name']), 0, 1)) ?>
                </div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--slate-900);"><?= esc($user['name']) ?></h2>
                <div style="color: var(--slate-500); font-size: 0.88rem; margin-top: 0.2rem;"><?= esc($user['email']) ?></div>
                
                <div style="margin-top: 1rem;">
                    <span class="badge badge-<?= $user['status'] ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($user['status'])) ?>
                    </span>
                </div>

                <div style="margin-top: 1.5rem; text-align: left; border-top: 1px solid var(--slate-100); padding-top: 1.25rem;">
                    <div style="font-size: 0.76rem; font-weight: 700; text-transform: uppercase; color: var(--slate-400); margin-bottom: 0.5rem;">Affiliation</div>
                    <div style="font-size: 0.88rem; color: var(--slate-700); margin-bottom: 0.8rem;">
                        <strong>Branch:</strong> <?= esc($user['branch_name'] ?? 'Corporate Headquarters') ?>
                    </div>
                    <div style="font-size: 0.88rem; color: var(--slate-700); margin-bottom: 0.8rem;">
                        <strong>Phone:</strong> <?= esc($user['phone'] ?: 'None registered') ?>
                    </div>
                    <div style="font-size: 0.88rem; color: var(--slate-700); margin-bottom: 0.8rem;">
                        <strong>Member Since:</strong> <?= esc(date('M d, Y', strtotime($user['created_at']))) ?>
                    </div>
                    <div style="font-size: 0.88rem; color: var(--slate-700);">
                        <strong>Last Login:</strong> <?= $user['last_login_at'] ? esc(date('M d, Y h:i A', strtotime($user['last_login_at']))) : 'Never' ?>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; text-align: left; border-top: 1px solid var(--slate-100); padding-top: 1.25rem;">
                    <div style="font-size: 0.76rem; font-weight: 700; text-transform: uppercase; color: var(--slate-400); margin-bottom: 0.5rem;">Assigned Roles</div>
                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                        <?php if (empty($user['roles'])): ?>
                            <span style="color: var(--slate-400); font-size: 0.82rem;">No roles currently assigned.</span>
                        <?php else: ?>
                            <?php foreach ($user['roles'] as $r): ?>
                                <span class="badge-role" style="font-size: 0.78rem; padding: 0.3rem 0.6rem;"><?= esc($r['name']) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Audit Activity -->
    <div class="form-col-6" style="flex: 0 0 66.6666%; max-width: 66.6666%;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Audit Trail</h3>
                <div class="card-subtitle">Recent actions logged by this account</div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activities)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">No audit records found for this account.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($activities as $act): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-role"><?= esc($act['action']) ?></span>
                                        </td>
                                        <td style="font-weight: 600; color: var(--slate-800);"><?= esc($act['module']) ?></td>
                                        <td style="font-size: 0.84rem;"><?= esc($act['description'] ?: '—') ?></td>
                                        <td style="font-family: monospace; font-size: 0.78rem; color: var(--slate-500);"><?= esc($act['ip_address']) ?></td>
                                        <td style="font-size: 0.8rem; color: var(--slate-600); white-space: nowrap;">
                                            <?= esc(date('M d, Y h:i A', strtotime($act['created_at']))) ?>
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
