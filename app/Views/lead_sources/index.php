<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Lead Sources</span>
        </div>
        <h1 class="page-title">Lead Acquisition Sources</h1>
        <p class="page-subtitle">Configure marketing channels and attribution sources for inbound leads.</p>
    </div>
    <div>
        <a href="/lead-sources/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Lead Source
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="get" action="/lead-sources" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <input type="text" name="search" class="form-control" placeholder="Search by source name, slug, or description..." value="<?= esc($search) ?>">
        </div>
        <div style="width: 180px;">
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Only</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if (!empty($search) || !empty($status)): ?>
                <a href="/lead-sources" class="btn btn-secondary" style="margin-left: 0.5rem;">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Sources Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Source Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sources)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No lead sources found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sources as $s): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--slate-900);">
                                <?= esc($s['name']) ?>
                            </td>
                            <td>
                                <code><?= esc($s['slug']) ?></code>
                            </td>
                            <td style="color: var(--slate-600); max-width: 300px;">
                                <?= esc($s['description'] ?: '—') ?>
                            </td>
                            <td>
                                <?php if ($s['status'] === 'active'): ?>
                                    <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">Active</span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem;">
                                    <a href="/lead-sources/edit/<?= esc($s['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-action="/lead-sources/delete/<?= esc($s['id']) ?>" data-item="<?= esc($s['name']) ?>">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager): ?>
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
