<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Property Units</span>
        </div>
        <h1 class="page-title">Property Unit Inventory</h1>
        <p class="page-subtitle">Track and configure individual apartments, floors, flats, commercial suites, and their availability</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('units.create', session()->get('permissions') ?? [])): ?>
            <a href="/units/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Unit
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<form method="GET" action="/units" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search unit #, flat type, project..." value="<?= esc($search) ?>">
        </div>

        <select name="project_id" class="form-control" style="width: auto;">
            <option value="">All Projects</option>
            <?php foreach ($projects as $p): ?>
                <option value="<?= esc($p['id']) ?>" <?= $projectId == $p['id'] ? 'selected' : '' ?>>
                    <?= esc($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-control" style="width: auto;">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $st): ?>
                <option value="<?= esc($st) ?>" <?= $status === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($projectId) || !empty($status) || !empty($flatType)): ?>
            <a href="/units" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unit Number</th>
                        <th>Project</th>
                        <th>Tower</th>
                        <th>Floor</th>
                        <th>Flat Type</th>
                        <th>Carpet / Built-up Area</th>
                        <th>Unit Price (₹)</th>
                        <th>Availability</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($units)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No property units found matching your search.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($units as $u): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--primary);">
                                        <a href="/units/view/<?= esc($u['id']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($u['unit_number']) ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($u['facing'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($u['facing']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($u['project_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><code><?= esc($u['project_code']) ?></code></div>
                                </td>
                                <td><?= esc($u['tower_name'] ?: 'Standalone') ?></td>
                                <td>Floor <?= esc($u['floor']) ?></td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-800); font-weight: 600;">
                                        <?= esc($u['flat_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div><?= number_format((float)$u['built_up_area'], 2) ?> sq.ft</div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= number_format((float)$u['carpet_area'], 2) ?> carpet</div>
                                </td>
                                <td style="font-weight: 700; color: var(--slate-900);">
                                    ₹<?= number_format((float)$u['unit_price'], 2) ?>
                                </td>
                                <td>
                                    <?= \App\Libraries\PropertyStatus::renderBadge($u['availability_status']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/units/view/<?= esc($u['id']) ?>" class="btn btn-sm btn-secondary">View</a>
                                        <?php if (session()->get('is_super_admin') || in_array('units.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/units/edit/<?= esc($u['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('units.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/units/delete/<?= esc($u['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this unit?');">
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
