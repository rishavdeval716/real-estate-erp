<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Follow-ups</span>
        </div>
        <h1 class="page-title">Follow-up Management</h1>
        <p class="page-subtitle">Schedule, track, and complete phone calls, WhatsApp messages, and meetings.</p>
    </div>
</div>

<!-- Follow-up Category Metrics -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <a href="/followups?tab=today" style="text-decoration: none; color: inherit;">
        <div class="metric-card primary" style="<?= $viewType === 'today' ? 'outline: 2px solid var(--primary);' : '' ?>">
            <div>
                <div class="metric-label">Today's Follow-ups</div>
                <div class="metric-value"><?= number_format($kpi['today']) ?></div>
                <div class="metric-meta"><span>Due before midnight</span></div>
            </div>
        </div>
    </a>
    <a href="/followups?tab=overdue" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="border-left: 4px solid var(--danger); <?= $viewType === 'overdue' ? 'outline: 2px solid var(--danger);' : '' ?>">
            <div>
                <div class="metric-label">Overdue Follow-ups</div>
                <div class="metric-value" style="color: var(--danger);"><?= number_format($kpi['overdue']) ?></div>
                <div class="metric-meta"><span>Missed schedules</span></div>
            </div>
        </div>
    </a>
    <a href="/followups?tab=upcoming" style="text-decoration: none; color: inherit;">
        <div class="metric-card info" style="<?= $viewType === 'upcoming' ? 'outline: 2px solid var(--info);' : '' ?>">
            <div>
                <div class="metric-label">Upcoming Schedule</div>
                <div class="metric-value"><?= number_format($kpi['upcoming']) ?></div>
                <div class="metric-meta"><span>Future appointments</span></div>
            </div>
        </div>
    </a>
    <a href="/followups?tab=completed" style="text-decoration: none; color: inherit;">
        <div class="metric-card success" style="<?= $viewType === 'completed' ? 'outline: 2px solid var(--success);' : '' ?>">
            <div>
                <div class="metric-label">Completed Log</div>
                <div class="metric-value"><?= number_format($kpi['completed']) ?></div>
                <div class="metric-meta"><span>Successful engagements</span></div>
            </div>
        </div>
    </a>
</div>

<!-- Navigation Tabs -->
<div class="tabs-nav" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--slate-200); margin-bottom: 1.5rem;">
    <a href="/followups?tab=today" class="tab-btn <?= $viewType === 'today' ? 'active' : '' ?>">Today's Tasks (<?= $kpi['today'] ?>)</a>
    <a href="/followups?tab=overdue" class="tab-btn <?= $viewType === 'overdue' ? 'active' : '' ?>" style="<?= $kpi['overdue'] > 0 ? 'color: var(--danger);' : '' ?>">Overdue (<?= $kpi['overdue'] ?>)</a>
    <a href="/followups?tab=upcoming" class="tab-btn <?= $viewType === 'upcoming' ? 'active' : '' ?>">Upcoming (<?= $kpi['upcoming'] ?>)</a>
    <a href="/followups?tab=completed" class="tab-btn <?= $viewType === 'completed' ? 'active' : '' ?>">Completed (<?= $kpi['completed'] ?>)</a>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th>Lead Name / Code</th>
                    <th>Contact Phone</th>
                    <th>Scheduled For</th>
                    <th>Assigned Executive</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Notes / Outcome</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($followups)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No follow-up tasks currently in this queue.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($followups as $fu): ?>
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: var(--slate-900);">
                                    <?= esc($fu['followup_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900);">
                                    <a href="/leads/view/<?= esc($fu['lead_id']) ?>" style="color: inherit; text-decoration: none;">
                                        <?= esc($fu['first_name'] . ' ' . $fu['last_name']) ?>
                                    </a>
                                </div>
                                <div style="font-family: monospace; font-size: 0.75rem; color: var(--slate-500);">
                                    <?= esc($fu['lead_code']) ?>
                                </div>
                            </td>
                            <td>
                                <a href="tel:<?= esc($fu['lead_phone']) ?>" style="font-weight: 600; color: var(--primary); text-decoration: none;">
                                    <?= esc($fu['lead_phone']) ?>
                                </a>
                            </td>
                            <td style="font-weight: 600; color: var(--slate-900); white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($fu['scheduled_at'])) ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-700);"><?= esc($fu['assigned_to_name']) ?></span>
                            </td>
                            <td>
                                <?= \App\Libraries\LeadStatus::renderPriorityBadge($fu['lead_priority'] ?: 'Medium') ?>
                            </td>
                            <td>
                                <?php if ($fu['status'] === 'Completed'): ?>
                                    <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #047857;">Completed</span>
                                <?php elseif ($fu['status'] === 'Cancelled'): ?>
                                    <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #fef2f2; color: #b91c1c;">Cancelled</span>
                                <?php else: ?>
                                    <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 250px; font-size: 0.85rem;">
                                <div><?= esc($fu['notes'] ?: '—') ?></div>
                                <?php if ($fu['outcome']): ?>
                                    <div style="font-size: 0.75rem; color: var(--success); font-weight: 600; margin-top: 0.2rem;">
                                        Outcome: <?= esc($fu['outcome']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <?php if ($fu['status'] === 'Pending'): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCompleteModal(<?= esc($fu['id']) ?>)">Complete</button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="openRescheduleModal(<?= esc($fu['id']) ?>, '<?= esc($fu['scheduled_at']) ?>')">Reschedule</button>
                                    <?php else: ?>
                                        <a href="/leads/view/<?= esc($fu['lead_id']) ?>" class="btn btn-sm btn-secondary">Lead Details</a>
                                    <?php endif; ?>
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

<!-- Modal: Complete Follow-up -->
<div id="modal-complete" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Complete Follow-up Task</h3>
        <form id="form-complete" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Outcome / Result <span style="color: var(--danger);">*</span></label>
                <input type="text" name="outcome" class="form-control" required placeholder="e.g. Discussed pricing; agreed to weekend site visit">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Detailed Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Key highlights from client call..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Schedule Next Follow-up (Optional)</label>
                <input type="datetime-local" name="next_followup_at" class="form-control">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-complete')">Cancel</button>
                <button type="submit" class="btn btn-primary">Mark Completed</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reschedule Follow-up -->
<div id="modal-reschedule" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 450px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Reschedule Follow-up</h3>
        <form id="form-reschedule" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">New Date & Time <span style="color: var(--danger);">*</span></label>
                <input type="datetime-local" name="scheduled_at" id="reschedule-date" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Reschedule Reason</label>
                <textarea name="reason" class="form-control" rows="2" placeholder="Client requested later time / out of town..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-reschedule')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save New Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCompleteModal(id) {
    document.getElementById('form-complete').action = '/followups/complete/' + id;
    document.getElementById('modal-complete').style.display = 'flex';
}

function openRescheduleModal(id, currentSchedule) {
    document.getElementById('form-reschedule').action = '/followups/reschedule/' + id;
    document.getElementById('reschedule-date').value = currentSchedule.replace(' ', 'T').substring(0, 16);
    document.getElementById('modal-reschedule').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

<?= $this->endSection() ?>
