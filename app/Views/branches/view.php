<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/branches">Branches</a>
            <span class="separator">/</span>
            <span>View</span>
        </div>
        <h1 class="page-title"><?= esc($branch['name']) ?></h1>
        <p class="page-subtitle">Branch Code: <code><?= esc($branch['code']) ?></code> &bull; <?= esc($branch['company_name'] ?? 'Corporate') ?></p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('branches.edit', session()->get('permissions') ?? [])): ?>
            <a href="/branches/edit/<?= esc($branch['id']) ?>" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Edit Branch
            </a>
        <?php endif; ?>
        <a href="/branches" class="btn btn-secondary">Back to Branches</a>
    </div>
</div>

<div class="form-row">
    <!-- Branch Details -->
    <div class="form-col-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Branch Overview</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <span class="badge badge-<?= $branch['status'] ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($branch['status'])) ?>
                    </span>
                </div>

                <div style="font-size: 0.88rem; color: var(--slate-700); line-height: 1.8;">
                    <div><strong>Branch Code:</strong> <code><?= esc($branch['code']) ?></code></div>
                    <div><strong>Branch Manager:</strong> <?= esc($branch['manager_name'] ?: 'Not Assigned') ?></div>
                    <div><strong>Email:</strong> <?= esc($branch['email'] ?: '—') ?></div>
                    <div><strong>Phone:</strong> <?= esc($branch['phone'] ?: '—') ?></div>
                    <div style="margin-top: 0.75rem; border-top: 1px solid var(--slate-100); padding-top: 0.75rem;">
                        <strong>Address:</strong><br>
                        <?= nl2br(esc($branch['address'] ?: '—')) ?><br>
                        <?= esc($branch['city'] ? $branch['city'] . ', ' : '') ?>
                        <?= esc($branch['state'] ? $branch['state'] . ' ' : '') ?>
                        <?= esc($branch['pincode'] ? ' - ' . $branch['pincode'] : '') ?><br>
                        <?= esc($branch['country'] ?: 'India') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Assigned to this Branch -->
    <div class="form-col-6" style="flex: 0 0 66.6666%; max-width: 66.6666%;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Staff / Personnel Assigned</h3>
                    <div class="card-subtitle"><?= count($users) ?> registered employees</div>
                </div>
                <?php if (session()->get('is_super_admin') || in_array('users.create', session()->get('permissions') ?? [])): ?>
                    <a href="/users/create" class="btn btn-sm btn-secondary">Add Staff</a>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">No staff accounts currently associated with this branch.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($u['name']) ?></td>
                                        <td style="font-size: 0.84rem; color: var(--slate-600);"><?= esc($u['email']) ?></td>
                                        <td><?= esc($u['phone'] ?: '—') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $u['status'] ?>">
                                                <span class="badge-dot"></span>
                                                <?= ucfirst(esc($u['status'])) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/users/view/<?= esc($u['id']) ?>" class="btn-icon" title="View User">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
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
