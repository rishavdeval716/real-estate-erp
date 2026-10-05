<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Company</span>
        </div>
        <h1 class="page-title">Corporate Organization Profile</h1>
        <p class="page-subtitle">Master enterprise profile, corporate identity, and registered offices</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('company.edit', session()->get('permissions') ?? [])): ?>
            <a href="/company/edit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Edit Company
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <!-- Corporate Summary Card -->
    <div class="form-col-4">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 2.5rem 1.5rem;">
                <?php if (!empty($company['logo']) && file_exists(FCPATH . ltrim($company['logo'], '/'))): ?>
                    <img src="<?= esc($company['logo']) ?>" alt="Company Logo" style="max-height: 80px; max-width: 180px; object-fit: contain; margin-bottom: 1.25rem; border-radius: var(--radius-sm);">
                <?php else: ?>
                    <div class="user-avatar-sm" style="width: 72px; height: 72px; font-size: 1.8rem; margin: 0 auto 1.25rem; background: linear-gradient(135deg, #1e40af, #3b82f6);">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>
                    </div>
                <?php endif; ?>

                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--slate-900);"><?= esc($company['name']) ?></h2>
                <div style="font-size: 0.84rem; color: var(--slate-500); margin-top: 0.25rem;"><?= esc($company['tax_number'] ? 'Tax / GST: ' . $company['tax_number'] : 'Tax ID not configured') ?></div>

                <div style="margin-top: 1rem;">
                    <span class="badge badge-<?= $company['status'] ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($company['status'])) ?>
                    </span>
                </div>

                <div style="margin-top: 1.5rem; text-align: left; border-top: 1px solid var(--slate-100); padding-top: 1.25rem; font-size: 0.88rem; color: var(--slate-700);">
                    <div style="margin-bottom: 0.75rem;">
                        <strong>Email:</strong> <?= esc($company['email'] ?: '—') ?>
                    </div>
                    <div style="margin-bottom: 0.75rem;">
                        <strong>Primary Phone:</strong> <?= esc($company['phone'] ?: '—') ?>
                    </div>
                    <?php if (!empty($company['alternate_phone'])): ?>
                        <div style="margin-bottom: 0.75rem;">
                            <strong>Alt Phone:</strong> <?= esc($company['alternate_phone']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($company['website'])): ?>
                        <div style="margin-bottom: 0.75rem;">
                            <strong>Website:</strong> <a href="<?= esc($company['website']) ?>" target="_blank" rel="noopener"><?= esc($company['website']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Address & Branches -->
    <div class="form-col-6" style="flex: 0 0 66.6666%; max-width: 66.6666%;">
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <h3 class="card-title">Corporate Address & Overview</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.76rem; font-weight: 700; text-transform: uppercase; color: var(--slate-400); margin-bottom: 0.35rem;">Registered Corporate Address</div>
                    <div style="font-size: 0.95rem; color: var(--slate-800); line-height: 1.6;">
                        <?= nl2br(esc($company['address'] ?: 'No address registered')) ?><br>
                        <?= esc($company['city'] ? $company['city'] . ', ' : '') ?>
                        <?= esc($company['state'] ? $company['state'] . ' ' : '') ?>
                        <?= esc($company['pincode'] ? ' - ' . $company['pincode'] : '') ?><br>
                        <?= esc($company['country'] ?: 'India') ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.76rem; font-weight: 700; text-transform: uppercase; color: var(--slate-400); margin-bottom: 0.35rem;">Organization Scope / Description</div>
                    <p style="font-size: 0.9rem; color: var(--slate-600); line-height: 1.6;">
                        <?= nl2br(esc($company['description'] ?: 'No description provided.')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Associated Branches -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">Operational Branches</h3>
                    <div class="card-subtitle"><?= count($branches) ?> registered regional hubs</div>
                </div>
                <?php if (session()->get('is_super_admin') || in_array('branches.create', session()->get('permissions') ?? [])): ?>
                    <a href="/branches/create" class="btn btn-sm btn-secondary">Add Branch</a>
                <?php endif; ?>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Code</th>
                                <th>City</th>
                                <th>Manager</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($branches)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 2rem;">No branches registered under this company yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($branches as $b): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($b['name']) ?></td>
                                        <td><code><?= esc($b['code']) ?></code></td>
                                        <td><?= esc($b['city'] ?: '—') ?></td>
                                        <td><?= esc($b['manager_name'] ?: '—') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $b['status'] ?>">
                                                <span class="badge-dot"></span>
                                                <?= ucfirst(esc($b['status'])) ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/branches/view/<?= esc($b['id']) ?>" class="btn-icon" title="View Branch">
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
