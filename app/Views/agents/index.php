<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Agents & Brokers</span>
        </div>
        <h1 class="page-title">Agent & Broker Directory</h1>
        <p class="page-subtitle">Manage external real estate brokers, channel partner networks, in-house agents, and commission tiers.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/agents/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Onboard Agent / Broker
        </a>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Channel Network</div>
            <div class="metric-value"><?= number_format($totalAgents) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Registered brokers & agents</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Active Agents</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($activeAgents) ?></div>
            <div class="metric-meta" style="color: var(--success);">Authorized to sell</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">External Brokers</div>
            <div class="metric-value" style="color: var(--info);"><?= number_format($brokersCount) ?></div>
            <div class="metric-meta" style="color: var(--info);">Independent agencies</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Channel Partners</div>
            <div class="metric-value" style="color: var(--warning);"><?= number_format($partnersCount) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Corporate institutional partners</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/agents" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, agent code, agency, license, phone..." value="<?= esc($search) ?>">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Agent Type</label>
                <select name="agent_type" class="form-control">
                    <option value="">All Agent Types</option>
                    <option value="External Broker" <?= $agentType === 'External Broker' ? 'selected' : '' ?>>External Broker</option>
                    <option value="Channel Partner" <?= $agentType === 'Channel Partner' ? 'selected' : '' ?>>Channel Partner</option>
                    <option value="Internal Agent" <?= $agentType === 'Internal Agent' ? 'selected' : '' ?>>Internal Agent</option>
                    <option value="Agency" <?= $agentType === 'Agency' ? 'selected' : '' ?>>Agency</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/agents" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Agents Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Agent Code</th>
                        <th>Name & Agency</th>
                        <th>Channel Type</th>
                        <th>RERA License</th>
                        <th>Contact</th>
                        <th>Commission %</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($agents)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">No agents found.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($agents as $a): ?>
                    <tr>
                        <td><strong style="color: var(--primary);"><?= esc($a['agent_code']) ?></strong></td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($a['first_name'] . ' ' . $a['last_name']) ?></div>
                            <?php if ($a['agency_name']): ?>
                            <div style="font-size: 0.8rem; color: var(--slate-500);"><?= esc($a['agency_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= esc($a['agent_type']) ?></span>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-size: 0.82rem;"><?= esc($a['license_number'] ?: 'Unregistered') ?></span>
                        </td>
                        <td>
                            <div><?= esc($a['phone']) ?></div>
                            <div style="font-size: 0.8rem; color: var(--slate-500);"><?= esc($a['email']) ?></div>
                        </td>
                        <td>
                            <strong style="color: var(--success);"><?= number_format((float)$a['commission_rate'], 2) ?>%</strong>
                        </td>
                        <td>
                            <span class="badge <?= $a['status'] === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst(esc($a['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.4rem;">
                                <a href="/agents/view/<?= $a['id'] ?>" class="btn btn-sm btn-secondary">Performance</a>
                                <a href="/agents/edit/<?= $a['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
