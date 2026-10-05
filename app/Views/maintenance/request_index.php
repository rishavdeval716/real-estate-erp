<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>Work Orders</span>
        </div>
        <h1 class="page-title">Maintenance Work Orders & Tickets</h1>
        <p class="page-subtitle">Track building repairs, technician assignments, SLA resolution deadlines, and closed tickets.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/maintenance/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Work Order
        </a>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Work Orders</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Unassigned / Open</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);"><?= esc($kpi['open']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Assigned</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-600);"><?= esc($kpi['assigned']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">In Progress</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['in_progress']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Resolved / Closed</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['resolved']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/maintenance" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Ticket #, description, tenant, property..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Priority</label>
                <select name="priority" class="form-control">
                    <option value="">All Priorities</option>
                    <option value="urgent" <?= $filters['priority'] === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    <option value="high" <?= $filters['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                    <option value="medium" <?= $filters['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="low" <?= $filters['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="open" <?= $filters['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="assigned" <?= $filters['status'] === 'assigned' ? 'selected' : '' ?>>Assigned</option>
                    <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $filters['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="closed" <?= $filters['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Property</label>
                <select name="property_id" class="form-control">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $pr): ?>
                    <option value="<?= $pr['id'] ?>" <?= $filters['property_id'] == $pr['id'] ? 'selected' : '' ?>><?= esc($pr['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/maintenance" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Requests Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Category</th>
                        <th>Property & Unit</th>
                        <th>Logged By / Tenant</th>
                        <th>Assigned Tech</th>
                        <th>Priority</th>
                        <th>SLA Due Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No maintenance requests found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td>
                            <a href="/maintenance/view/<?= $r['id'] ?>" style="font-weight: 600; color: var(--primary-600);">
                                <?= esc($r['ticket_number']) ?>
                            </a>
                            <div style="font-size: 0.75rem; color: var(--slate-400);"><?= date('d M H:i', strtotime($r['created_date'])) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="text-transform: capitalize; font-weight: 600;">
                                <?= esc($r['category']) ?>
                            </span>
                            <?php if (!empty($r['asset_name'])): ?>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Asset: <?= esc($r['asset_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($r['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($r['unit_number'] ?? 'Common Area') ?></div>
                        </td>
                        <td>
                            <?php if (!empty($r['tenant_name'])): ?>
                            <div style="font-weight: 500;"><?= esc($r['tenant_name']) ?></div>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">Facility Admin</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($r['technician_name'])): ?>
                            <div style="font-weight: 500; color: var(--slate-800);"><?= esc($r['technician_name']) ?></div>
                            <?php else: ?>
                            <span class="badge badge-danger">Unassigned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $pBadge = match($r['priority']) {
                                'urgent' => 'badge-danger',
                                'high'   => 'badge-warning',
                                'medium' => 'badge-info',
                                default  => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $pBadge ?>" style="text-transform: capitalize;">
                                <?= esc($r['priority']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($r['sla_due_date'])): ?>
                            <div style="font-size: 0.85rem; font-weight: 500; color: <?= strtotime($r['sla_due_date']) < time() && !in_array($r['status'], ['resolved', 'closed']) ? 'var(--rose-600)' : 'var(--slate-700)' ?>;">
                                <?= date('d M H:i', strtotime($r['sla_due_date'])) ?>
                            </div>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $sBadge = match($r['status']) {
                                'resolved', 'closed' => 'badge-success',
                                'in_progress'        => 'badge-primary',
                                'assigned'           => 'badge-info',
                                default              => 'badge-danger',
                            };
                            ?>
                            <span class="badge <?= $sBadge ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($r['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="/maintenance/view/<?= $r['id'] ?>" class="btn btn-sm btn-secondary">Manage</a>
                            <a href="/maintenance/voucher/<?= $r['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print Sheet</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
    <div class="card-footer" style="padding: 1rem;">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
