<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Leads</span>
        </div>
        <h1 class="page-title">Lead Management</h1>
        <p class="page-subtitle">Track, qualify, assign, and convert real estate buyer inquiries.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/pipeline" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
            Kanban Pipeline
        </a>
        <a href="/leads/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Lead
        </a>
    </div>
</div>

<!-- Dynamic KPI Summary Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Leads</div>
            <div class="metric-value"><?= number_format($kpi['total']) ?></div>
            <div class="metric-meta"><span>Active repository</span></div>
        </div>
    </div>
    <div class="metric-card info">
        <div>
            <div class="metric-label">New Leads</div>
            <div class="metric-value"><?= number_format($kpi['new']) ?></div>
            <div class="metric-meta"><span>Awaiting contact</span></div>
        </div>
    </div>
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Contacted</div>
            <div class="metric-value"><?= number_format($kpi['contacted']) ?></div>
            <div class="metric-meta"><span>Engagement started</span></div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Qualified</div>
            <div class="metric-value"><?= number_format($kpi['qualified']) ?></div>
            <div class="metric-meta"><span>Budget & timeline fit</span></div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Deals Won</div>
            <div class="metric-value"><?= number_format($kpi['won']) ?></div>
            <div class="metric-meta" style="color: var(--success);"><span>Ready for booking</span></div>
        </div>
    </div>
    <div class="metric-card" style="border-left: 4px solid var(--danger);">
        <div>
            <div class="metric-label">Lost Leads</div>
            <div class="metric-value" style="color: var(--danger);"><?= number_format($kpi['lost']) ?></div>
            <div class="metric-meta"><span>Disqualified / Closed</span></div>
        </div>
    </div>
</div>

<!-- Advanced Multi-Filter Card -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="get" action="/leads">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Code, Name, Phone, Email..." value="<?= esc($filters['search']) ?>">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Status</label>
                <select name="lead_status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= esc($st) ?>" <?= $filters['lead_status'] === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Pipeline Stage</label>
                <select name="lead_stage" class="form-control">
                    <option value="">All Stages</option>
                    <?php foreach ($stages as $sg): ?>
                        <option value="<?= esc($sg) ?>" <?= $filters['lead_stage'] === $sg ? 'selected' : '' ?>><?= esc($sg) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Priority</label>
                <select name="priority" class="form-control">
                    <option value="">All Priorities</option>
                    <?php foreach ($priorities as $pr): ?>
                        <option value="<?= esc($pr) ?>" <?= $filters['priority'] === $pr ? 'selected' : '' ?>><?= esc($pr) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Lead Source</label>
                <select name="lead_source_id" class="form-control">
                    <option value="">All Sources</option>
                    <?php foreach ($sources as $src): ?>
                        <option value="<?= esc($src['id']) ?>" <?= (string)$filters['lead_source_id'] === (string)$src['id'] ? 'selected' : '' ?>><?= esc($src['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Assigned Executive</label>
                <select name="assigned_user_id" class="form-control">
                    <option value="">All Executives</option>
                    <?php foreach ($executives as $ex): ?>
                        <option value="<?= esc($ex['id']) ?>" <?= (string)$filters['assigned_user_id'] === (string)$ex['id'] ? 'selected' : '' ?>><?= esc($ex['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Project</label>
                <select name="project_id" class="form-control">
                    <option value="">All Projects</option>
                    <?php foreach ($projects as $prj): ?>
                        <option value="<?= esc($prj['id']) ?>" <?= (string)$filters['project_id'] === (string)$prj['id'] ? 'selected' : '' ?>><?= esc($prj['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Min Budget (₹)</label>
                <input type="number" name="min_budget" class="form-control" placeholder="Min" value="<?= esc($filters['min_budget'] ?? '') ?>">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.78rem;">Max Budget (₹)</label>
                <input type="number" name="max_budget" class="form-control" placeholder="Max" value="<?= esc($filters['max_budget'] ?? '') ?>">
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <a href="/leads" class="btn btn-secondary">Reset Filters</a>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>
</div>

<!-- Leads Data Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Lead Code</th>
                    <th>Lead Name</th>
                    <th>Contact</th>
                    <th>Source</th>
                    <th>Target Property</th>
                    <th>Budget</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Pipeline Stage</th>
                    <th>Assigned Executive</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No leads found matching your search and filter criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $l): ?>
                        <tr>
                            <td>
                                <a href="/leads/view/<?= esc($l['id']) ?>" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                    <code><?= esc($l['lead_code']) ?></code>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900);">
                                    <a href="/leads/view/<?= esc($l['id']) ?>" style="color: inherit; text-decoration: none;">
                                        <?= esc($l['first_name'] . ' ' . $l['last_name']) ?>
                                    </a>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);">
                                    Added <?= date('d M Y', strtotime($l['created_at'])) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; font-weight: 600;"><?= esc($l['phone']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($l['email'] ?: '—') ?></div>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--slate-700);">
                                    <?= esc($l['source_name'] ?: 'Direct') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($l['property_title'])): ?>
                                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--slate-800);"><?= esc($l['property_title']) ?></div>
                                <?php elseif (!empty($l['project_name'])): ?>
                                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--slate-800);"><?= esc($l['project_name']) ?></div>
                                <?php else: ?>
                                    <div style="font-size: 0.85rem; color: var(--slate-500);"><?= esc($l['preferred_location'] ?: 'General') ?></div>
                                <?php endif; ?>
                                <?php if (!empty($l['unit_number'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--primary);">Unit: <?= esc($l['unit_number']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((float)$l['budget_max'] > 0): ?>
                                    <div style="font-weight: 700; color: var(--slate-900);">
                                        ₹<?= (float)$l['budget_min'] > 0 ? number_format((float)$l['budget_min'] / 100000, 1) . 'L - ' : '' ?>
                                        <?= (float)$l['budget_max'] >= 10000000 ? number_format((float)$l['budget_max'] / 10000000, 2) . ' Cr' : number_format((float)$l['budget_max'] / 100000, 1) . ' L' ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--slate-400);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= \App\Libraries\LeadStatus::renderPriorityBadge($l['priority']) ?>
                            </td>
                            <td>
                                <?= \App\Libraries\LeadStatus::renderStatusBadge($l['lead_status']) ?>
                            </td>
                            <td>
                                <?= \App\Libraries\LeadStatus::renderStageBadge($l['lead_stage']) ?>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--slate-700);">
                                    <?= esc($l['assigned_to_name'] ?: 'Unassigned') ?>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <a href="/leads/view/<?= esc($l['id']) ?>" class="btn btn-sm btn-secondary" title="View Lead & Timeline">View</a>
                                    <a href="/leads/edit/<?= esc($l['id']) ?>" class="btn btn-sm btn-secondary" title="Edit Lead">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-action="/leads/delete/<?= esc($l['id']) ?>" data-item="<?= esc($l['lead_code']) ?>">Delete</button>
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
