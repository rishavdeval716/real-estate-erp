<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Audit Logs</span>
        </div>
        <h1 class="page-title">Security & Audit Logs</h1>
        <p class="page-subtitle">Immutable trace of authentication, account modifications, and system activity</p>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="/audit-logs" class="filter-bar">
    <div class="filter-group">
        <div class="search-input-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" class="form-control" placeholder="Search description, IP, actor..." value="<?= esc($search) ?>">
        </div>

        <select name="module" class="form-control" style="width: auto;">
            <option value="">All Modules</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?= esc($m) ?>" <?= $selectedModule === $m ? 'selected' : '' ?>>
                    <?= esc($m) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="action" class="form-control" style="width: auto;">
            <option value="">All Actions</option>
            <?php foreach ($actions as $a): ?>
                <option value="<?= esc($a) ?>" <?= $selectedAction === $a ? 'selected' : '' ?>>
                    <?= esc($a) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <?php if (!empty($search) || !empty($selectedModule) || !empty($selectedAction)): ?>
            <a href="/audit-logs" class="btn btn-sm" style="color: var(--slate-500);">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Actor</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Client / Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-title">No Audit Logs Found</div>
                                    <div class="empty-state-desc">No events matched your search criteria.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td style="font-size: 0.8rem; color: var(--slate-600); white-space: nowrap;">
                                    <?= esc(date('M d, Y h:i:s A', strtotime($log['created_at']))) ?>
                                </td>
                                <td>
                                    <?php
                                    $actionClass = 'badge-secondary';
                                    if (strpos($log['action'], 'LOGIN') !== false) $actionClass = 'badge-success';
                                    if (strpos($log['action'], 'LOGOUT') !== false) $actionClass = 'badge-secondary';
                                    if (strpos($log['action'], 'FAILED') !== false || strpos($log['action'], 'BLOCKED') !== false || strpos($log['action'], 'DELETE') !== false) $actionClass = 'badge-danger';
                                    if (strpos($log['action'], 'CREATE') !== false) $actionClass = 'badge-active';
                                    if (strpos($log['action'], 'UPDATE') !== false) $actionClass = 'badge-warning';
                                    ?>
                                    <span class="badge <?= $actionClass ?>">
                                        <?= esc($log['action']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--slate-800);"><?= esc($log['module']) ?></td>
                                <td>
                                    <?php if ($log['user_id']): ?>
                                        <div style="font-weight: 600; color: var(--slate-900); font-size: 0.85rem;"><?= esc($log['user_name'] ?? 'User #' . $log['user_id']) ?></div>
                                        <div style="font-size: 0.74rem; color: var(--slate-400);"><?= esc($log['user_email'] ?? '') ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.82rem;">Anonymous / System</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-700); max-width: 320px;">
                                    <?= esc($log['description'] ?: '—') ?>
                                </td>
                                <td style="font-family: monospace; font-size: 0.78rem; color: var(--slate-500); white-space: nowrap;">
                                    <?= esc($log['ip_address']) ?>
                                </td>
                                <td style="font-size: 0.74rem; color: var(--slate-400); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= esc($log['user_agent']) ?>">
                                    <?= esc($log['user_agent']) ?>
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
                <div class="pagination-info">Showing recorded log entries</div>
                <div class="pagination-links"><?= $pager->links() ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
