<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>Ticket <?= esc($ticket['ticket_number']) ?></span>
        </div>
        <h1 class="page-title">Work Order <?= esc($ticket['ticket_number']) ?></h1>
        <p class="page-subtitle">SLA target metrics, technician dispatch, resolution logs, and work authorization.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/maintenance/voucher/<?= $ticket['id'] ?>" class="btn btn-secondary" target="_blank">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Work Order Sheet
        </a>
    </div>
</div>

<!-- Header Summary Card -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-body" style="padding: 1.5rem 2rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--slate-400); text-transform: uppercase;">Ticket Reference & Category</div>
            <div style="font-size: 1.75rem; font-weight: 700;"><?= esc($ticket['ticket_number']) ?> &bull; <?= ucfirst(esc($ticket['category'])) ?></div>
            <div style="color: var(--slate-300); font-size: 0.9rem; margin-top: 0.25rem;">
                Property: <strong><?= esc($ticket['property_title']) ?></strong> <?= !empty($ticket['unit_number']) ? '(Unit ' . esc($ticket['unit_number']) . ')' : '(Common Area)' ?>
            </div>
        </div>

        <div style="display: flex; gap: 2rem; align-items: center;">
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--slate-400);">SLA Target Due</div>
                <div style="font-size: 1.2rem; font-weight: 700; color: <?= strtotime($ticket['sla_due_date'] ?? '') < time() && !in_array($ticket['status'], ['resolved', 'closed']) ? 'var(--rose-400)' : 'var(--emerald-400)' ?>;">
                    <?= !empty($ticket['sla_due_date']) ? date('d M Y, H:i', strtotime($ticket['sla_due_date'])) : 'Standard SLA' ?>
                </div>
            </div>
            <div>
                <?php
                $sBadge = match($ticket['status']) {
                    'resolved', 'closed' => 'badge-success',
                    'in_progress'        => 'badge-primary',
                    'assigned'           => 'badge-info',
                    default              => 'badge-danger',
                };
                ?>
                <span class="badge <?= $sBadge ?>" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; text-transform: capitalize;">
                    <?= str_replace('_', ' ', esc($ticket['status'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- Left Column: Defect Details & Resolution -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Defect Details Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Reported Defect & Specifications</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Category</div>
                        <div style="font-weight: 600; text-transform: capitalize; color: var(--slate-800);"><?= esc($ticket['category']) ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Subcategory</div>
                        <div style="font-weight: 600; color: var(--slate-800);"><?= esc($ticket['subcategory'] ?? 'General') ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Priority</div>
                        <span class="badge <?= $ticket['priority'] === 'urgent' ? 'badge-danger' : 'badge-warning' ?>" style="text-transform: capitalize;">
                            <?= esc($ticket['priority']) ?>
                        </span>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--slate-500); text-transform: uppercase;">Created On</div>
                        <div style="font-weight: 600; color: var(--slate-800);"><?= date('d M Y, H:i', strtotime($ticket['created_date'])) ?></div>
                    </div>
                </div>

                <div style="font-size: 0.8rem; font-weight: 600; color: var(--slate-700); margin-bottom: 0.25rem;">Defect Description:</div>
                <div style="background: var(--slate-50); padding: 1rem; border-radius: 6px; font-size: 0.9rem; line-height: 1.6; color: var(--slate-700); margin-bottom: 1rem;">
                    <?= nl2br(esc($ticket['description'])) ?>
                </div>

                <?php if (!empty($ticket['resolution'])): ?>
                <div style="font-size: 0.8rem; font-weight: 600; color: var(--emerald-700); margin-bottom: 0.25rem;">Technician Resolution Log:</div>
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 6px; font-size: 0.9rem; line-height: 1.6; color: #065f46;">
                    <?= nl2br(esc($ticket['resolution'])) ?>
                    <?php if (!empty($ticket['closed_date'])): ?>
                    <div style="font-size: 0.75rem; color: #047857; margin-top: 0.5rem;">Closed: <?= date('d M Y, H:i', strtotime($ticket['closed_date'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Status & Resolution Form -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Update Work Order Status & Log Resolution</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <form method="POST" action="/maintenance/status/<?= $ticket['id'] ?>">
                    <?= csrf_field() ?>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-weight: 500;">Workflow Status <span style="color: var(--rose-500);">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open (Pending Initial Inspection)</option>
                            <option value="assigned" <?= $ticket['status'] === 'assigned' ? 'selected' : '' ?>>Assigned (Technician Dispatched)</option>
                            <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress (Work Active)</option>
                            <option value="on_hold" <?= $ticket['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold (Waiting for Parts/Materials)</option>
                            <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Resolved (Repairs Finished)</option>
                            <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed (Verified & Signed Off)</option>
                            <option value="rejected" <?= $ticket['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (Invalid Request)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-weight: 500;">Resolution Notes / Corrective Actions Taken</label>
                        <textarea name="resolution" class="form-control" rows="3" placeholder="Explain parts replaced, tests conducted, or root cause resolved..."><?= esc($ticket['resolution'] ?? '') ?></textarea>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary">Update Status & Resolution</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Dispatch & Asset Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Technician Dispatch Card -->
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Technician Dispatch</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <?php if (!empty($ticket['technician_name'])): ?>
                <div style="font-size: 1.05rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">
                    <?= esc($ticket['technician_name']) ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.75rem;">
                    Skill: <?= esc($ticket['technician_skill'] ?? 'General') ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--slate-600); margin-bottom: 1.25rem;">
                    <strong>Contact:</strong> <?= esc($ticket['technician_mobile'] ?? 'N/A') ?>
                </div>
                <?php else: ?>
                <div style="color: var(--rose-600); font-weight: 600; margin-bottom: 1rem;">
                    Currently Unassigned
                </div>
                <?php endif; ?>

                <!-- Assign / Reassign Form -->
                <form method="POST" action="/maintenance/assign/<?= $ticket['id'] ?>">
                    <?= csrf_field() ?>
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--slate-600);">Assign / Change Technician</label>
                    <select name="technician_id" class="form-control" style="margin-bottom: 0.75rem;" required>
                        <option value="">-- Choose Specialist --</option>
                        <?php foreach ($technicians as $tech): ?>
                        <option value="<?= $tech['id'] ?>" <?= $ticket['assigned_technician_id'] == $tech['id'] ? 'selected' : '' ?>>
                            <?= esc($tech['name']) ?> (<?= esc($tech['skill']) ?>) &bull; <?= ucfirst(esc($tech['availability'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary" style="width: 100%;">
                        Dispatch Technician
                    </button>
                </form>
            </div>
        </div>

        <!-- Linked Asset Card -->
        <?php if (!empty($ticket['asset_name'])): ?>
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Linked Facility Asset</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">
                    <?= esc($ticket['asset_name']) ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--slate-500); margin-bottom: 0.75rem;">
                    Code: <?= esc($ticket['asset_code'] ?? 'N/A') ?>
                </div>
                <a href="/facility-assets" class="btn btn-sm btn-secondary" style="width: 100%; text-align: center;">View Asset Register</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tenant Requester Card -->
        <?php if (!empty($ticket['tenant_name'])): ?>
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Resident Requester</h3>
            </div>
            <div class="card-body" style="padding: 1.25rem;">
                <div style="font-weight: 600; color: var(--slate-800);"><?= esc($ticket['tenant_name']) ?></div>
                <div style="font-size: 0.85rem; color: var(--slate-500);">Contact: <?= esc($ticket['tenant_mobile'] ?? 'N/A') ?></div>
                <a href="/tenants/view/<?= $ticket['tenant_id'] ?>" class="btn btn-sm btn-secondary" style="margin-top: 0.75rem; width: 100%; text-align: center;">View Tenant</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
