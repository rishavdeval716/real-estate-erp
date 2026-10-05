<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Site Visits</span>
        </div>
        <h1 class="page-title">Site Visit Management</h1>
        <p class="page-subtitle">Schedule physical & virtual property tours, monitor attendee turnout, and record qualification feedback.</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <a href="/site-visits" style="text-decoration: none; color: inherit;">
        <div class="metric-card primary" style="<?= empty($status) ? 'outline: 2px solid var(--primary);' : '' ?>">
            <div>
                <div class="metric-label">Total Visits</div>
                <div class="metric-value"><?= number_format($kpi['total']) ?></div>
                <div class="metric-meta"><span>All time logged</span></div>
            </div>
        </div>
    </a>
    <a href="/site-visits?status=Scheduled" style="text-decoration: none; color: inherit;">
        <div class="metric-card info" style="<?= in_array($status, ['Scheduled', 'Confirmed']) ? 'outline: 2px solid var(--info);' : '' ?>">
            <div>
                <div class="metric-label">Upcoming / Scheduled</div>
                <div class="metric-value"><?= number_format($kpi['scheduled']) ?></div>
                <div class="metric-meta"><span>Confirmed client visits</span></div>
            </div>
        </div>
    </a>
    <a href="/site-visits" style="text-decoration: none; color: inherit;">
        <div class="metric-card warning">
            <div>
                <div class="metric-label">Today's Visits</div>
                <div class="metric-value"><?= number_format($kpi['today']) ?></div>
                <div class="metric-meta"><span>Scheduled for today</span></div>
            </div>
        </div>
    </a>
    <a href="/site-visits?status=Completed" style="text-decoration: none; color: inherit;">
        <div class="metric-card success" style="<?= $status === 'Completed' ? 'outline: 2px solid var(--success);' : '' ?>">
            <div>
                <div class="metric-label">Completed Tours</div>
                <div class="metric-value"><?= number_format($kpi['completed']) ?></div>
                <div class="metric-meta"><span>With visitor feedback</span></div>
            </div>
        </div>
    </a>
</div>

<!-- Status Filters -->
<div class="tabs-nav" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--slate-200); margin-bottom: 1.5rem; flex-wrap: wrap;">
    <a href="/site-visits" class="tab-btn <?= empty($status) ? 'active' : '' ?>">All Statuses</a>
    <?php foreach ($statuses as $st): ?>
        <a href="/site-visits?status=<?= urlencode($st) ?>" class="tab-btn <?= $status === $st ? 'active' : '' ?>"><?= esc($st) ?></a>
    <?php endforeach; ?>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Visit Code</th>
                    <th>Lead Name / Code</th>
                    <th>Property / Project</th>
                    <th>Visit Date & Time</th>
                    <th>Type</th>
                    <th>Executive</th>
                    <th>Status</th>
                    <th>Feedback / Rating</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($visits)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No site visit appointments found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($visits as $v): ?>
                        <tr>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: var(--primary); font-size: 0.82rem;">
                                    <?= esc($v['visit_code']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900);">
                                    <a href="/leads/view/<?= esc($v['lead_id']) ?>" style="color: inherit; text-decoration: none;">
                                        <?= esc($v['first_name'] . ' ' . $v['last_name']) ?>
                                    </a>
                                </div>
                                <div style="font-size: 0.76rem; color: var(--slate-500);">
                                    <?= esc($v['lead_code']) ?> &bull; <?= esc($v['lead_phone']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--slate-800);">
                                    <?= esc($v['property_title'] ?? $v['project_name'] ?? 'General Development') ?>
                                </div>
                                <?php if (!empty($v['unit_number'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">
                                        Unit <?= esc($v['unit_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: var(--slate-900); white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($v['scheduled_at'])) ?>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-size: 0.75rem;">
                                    <?= esc($v['visit_type']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-700);"><?= esc($v['assigned_agent_name'] ?? 'Unassigned') ?></span>
                            </td>
                            <td>
                                <?php
                                $badgeStyle = match($v['status']) {
                                    'Completed'   => 'background: #ecfdf5; color: #047857;',
                                    'Scheduled'   => 'background: #eff6ff; color: #1d4ed8;',
                                    'Confirmed'   => 'background: #f0fdf4; color: #15803d;',
                                    'Cancelled'   => 'background: #fef2f2; color: #b91c1c;',
                                    'No Show'     => 'background: #fff7ed; color: #c2410c;',
                                    'Rescheduled' => 'background: #fefce8; color: #a16207;',
                                    default       => 'background: #f1f5f9; color: #475569;'
                                };
                                ?>
                                <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; <?= $badgeStyle ?>">
                                    <?= esc($v['status']) ?>
                                </span>
                            </td>
                            <td style="max-width: 220px; font-size: 0.82rem;">
                                <?php if ($v['status'] === 'Completed'): ?>
                                    <div>
                                        <strong>Rating:</strong> 
                                        <?= str_repeat('★', (int)$v['rating']) ?><span style="color: #cbd5e1;"><?= str_repeat('★', 5 - (int)$v['rating']) ?></span>
                                        (<?= esc($v['interest_level']) ?>)
                                    </div>
                                    <?php if ($v['feedback']): ?>
                                        <div style="color: var(--slate-600); margin-top: 0.2rem; font-style: italic;">
                                            "<?= esc($v['feedback']) ?>"
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($v['next_action']): ?>
                                        <div style="font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-top: 0.15rem;">
                                            Next: <?= esc($v['next_action']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--slate-400);"><?= esc($v['remarks'] ?: 'Pending visit execution') ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <?php if (in_array($v['status'], ['Scheduled', 'Confirmed', 'Rescheduled'])): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCompleteVisitModal(<?= esc($v['id']) ?>)">Feedback</button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="openRescheduleVisitModal(<?= esc($v['id']) ?>, '<?= esc($v['scheduled_at']) ?>')">Reschedule</button>
                                        <button type="button" class="btn btn-sm" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;" onclick="openCancelVisitModal(<?= esc($v['id']) ?>)">Cancel</button>
                                    <?php else: ?>
                                        <a href="/leads/view/<?= esc($v['lead_id']) ?>" class="btn btn-sm btn-secondary">Lead View</a>
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

<!-- Modal: Complete Site Visit -->
<div id="modal-complete-visit" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 540px; margin: 1rem; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Complete Site Visit & Record Feedback</h3>
        <form id="form-complete-visit" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label">Client Rating (1-5)</label>
                        <select name="rating" class="form-control">
                            <option value="5">★★★★★ (5 - Excellent)</option>
                            <option value="4" selected>★★★★☆ (4 - Very Good)</option>
                            <option value="3">★★★☆☆ (3 - Neutral)</option>
                            <option value="2">★★☆☆☆ (2 - Poor)</option>
                            <option value="1">★☆☆☆☆ (1 - Disappointed)</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label">Client Interest Level <span style="color: var(--danger);">*</span></label>
                        <select name="interest_level" class="form-control" required>
                            <option value="High">High</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Low">Low</option>
                            <option value="Not Interested">Not Interested</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label class="form-label">Customer Feedback Notes</label>
                <textarea name="feedback" class="form-control" rows="2" placeholder="Client remarks about floor layout, amenities, sunlight, view..."></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label class="form-label">Agent Observation & Assessment</label>
                <textarea name="agent_observation" class="form-control" rows="2" placeholder="Executive observation on client decision timeline, family agreement..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label">Preferred Unit Selected</label>
                        <input type="text" name="preferred_unit" class="form-control" placeholder="e.g. Tower A - Unit 302">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label">Price Reaction / Budget Notes</label>
                        <input type="text" name="price_feedback" class="form-control" placeholder="e.g. Budget flexible / expecting 3% discount">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Next Action Recommended <span style="color: var(--danger);">*</span></label>
                <select name="next_action" class="form-control" required>
                    <option value="Negotiation">Negotiation (Advance lead stage)</option>
                    <option value="Follow-up">Regular Follow-up</option>
                    <option value="Alternative Property">Show Alternative Property</option>
                    <option value="Token Discussion">Token Discussion (Hold reservation)</option>
                    <option value="Lost">Mark Lost / Unqualified</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeVisitModal('modal-complete-visit')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Visit Feedback</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reschedule Site Visit -->
<div id="modal-reschedule-visit" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 450px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Reschedule Site Visit</h3>
        <form id="form-reschedule-visit" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">New Date & Time <span style="color: var(--danger);">*</span></label>
                <input type="datetime-local" name="scheduled_at" id="reschedule-visit-date" class="form-control" required>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeVisitModal('modal-reschedule-visit')">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Reschedule</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cancel Site Visit -->
<div id="modal-cancel-visit" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 450px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Cancel Site Visit</h3>
        <form id="form-cancel-visit" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Cancellation Reason</label>
                <textarea name="reason" class="form-control" rows="2" placeholder="e.g. Client requested cancellation or no longer interested"></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeVisitModal('modal-cancel-visit')">Dismiss</button>
                <button type="submit" class="btn" style="background: var(--danger); color: #fff;">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCompleteVisitModal(id) {
    document.getElementById('form-complete-visit').action = '/site-visits/complete/' + id;
    document.getElementById('modal-complete-visit').style.display = 'flex';
}

function openRescheduleVisitModal(id, currentSchedule) {
    document.getElementById('form-reschedule-visit').action = '/site-visits/reschedule/' + id;
    document.getElementById('reschedule-visit-date').value = currentSchedule.replace(' ', 'T').substring(0, 16);
    document.getElementById('modal-reschedule-visit').style.display = 'flex';
}

function openCancelVisitModal(id) {
    document.getElementById('form-cancel-visit').action = '/site-visits/cancel/' + id;
    document.getElementById('modal-cancel-visit').style.display = 'flex';
}

function closeVisitModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

<?= $this->endSection() ?>
