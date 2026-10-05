<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>Resident Complaints</span>
        </div>
        <h1 class="page-title">Resident Complaints & Grievance Desk</h1>
        <p class="page-subtitle">Track resident concerns, review status workflows, log resolutions, and record satisfaction feedback.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openCreateComplaintModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Log Resident Grievance
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Grievances</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Submitted</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);"><?= esc($kpi['submitted']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">In Review</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['in_review']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">In Progress</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-600);"><?= esc($kpi['in_progress']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Resolved</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['resolved']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/complaints" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Complaint code, description, resident, property..." value="<?= esc($filters['search']) ?>">
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
                    <option value="submitted" <?= $filters['status'] === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                    <option value="in_review" <?= $filters['status'] === 'in_review' ? 'selected' : '' ?>>In Review</option>
                    <option value="in_progress" <?= $filters['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $filters['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/complaints" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Complaints Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type / Category</th>
                        <th>Resident / Tenant</th>
                        <th>Premises</th>
                        <th>Priority</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Satisfaction</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaints)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No resident grievances recorded.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($c['complaint_code']) ?></strong>
                            <div style="font-size: 0.75rem; color: var(--slate-400);"><?= date('d M Y', strtotime($c['created_at'])) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="font-weight: 600; text-transform: capitalize;"><?= esc($c['complaint_type']) ?></span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($c['tenant_name'] ?? 'Facility Resident') ?></div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 500;"><?= esc($c['property_title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);">Unit: <?= esc($c['unit_number'] ?? 'Common Area') ?></div>
                        </td>
                        <td>
                            <?php
                            $pBadge = match($c['priority']) {
                                'urgent' => 'badge-danger',
                                'high'   => 'badge-warning',
                                'medium' => 'badge-info',
                                default  => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $pBadge ?>" style="text-transform: capitalize;">
                                <?= esc($c['priority']) ?>
                            </span>
                        </td>
                        <td style="max-width: 250px;">
                            <div style="font-size: 0.85rem; color: var(--slate-700); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($c['description']) ?>">
                                <?= esc($c['description']) ?>
                            </div>
                            <?php if (!empty($c['resolution'])): ?>
                            <div style="font-size: 0.75rem; color: var(--emerald-600); margin-top: 0.2rem;">
                                &check; <?= esc($c['resolution']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $sBadge = match($c['status']) {
                                'resolved'    => 'badge-success',
                                'in_progress' => 'badge-primary',
                                'in_review'   => 'badge-warning',
                                'rejected'    => 'badge-danger',
                                default       => 'badge-secondary',
                            };
                            ?>
                            <span class="badge <?= $sBadge ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($c['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($c['feedback_rating'])): ?>
                            <span style="color: #f59e0b; font-weight: 700;"><?= str_repeat('★', (int)$c['feedback_rating']) ?></span>
                            <span style="font-size: 0.75rem; color: var(--slate-500);">(<?= (int)$c['feedback_rating'] ?>/5)</span>
                            <?php else: ?>
                            <span style="color: var(--slate-400); font-size: 0.8rem;">Pending rating</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openStatusModal(<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)">Update</button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openFeedbackModal(<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>)">Feedback</button>
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

<!-- Modal: Log Complaint -->
<div id="createComplaintModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Log Resident Grievance</h3>
            <button type="button" onclick="closeCreateComplaintModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/complaints/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Target Property <span style="color: var(--rose-500);">*</span></label>
                    <select name="property_id" class="form-control" required>
                        <option value="">-- Choose Property --</option>
                        <?php foreach ($properties as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Unit (Optional)</label>
                        <select name="property_unit_id" class="form-control">
                            <option value="">-- Common Area --</option>
                            <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>">Unit <?= esc($u['unit_number']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Resident / Tenant</label>
                        <select name="tenant_id" class="form-control">
                            <option value="">-- Unspecified Resident --</option>
                            <?php foreach ($tenants as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= esc($t['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Complaint Type <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="complaint_type" class="form-control" placeholder="e.g. Noise Disturbance, Water Seepage" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Priority <span style="color: var(--rose-500);">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Grievance Details <span style="color: var(--rose-500);">*</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the incident or issue..." required></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeCreateComplaintModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Update Complaint Status -->
<div id="statusModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalStatusTitle" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Update Complaint Status</h3>
            <button type="button" onclick="closeStatusModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="statusForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Status <span style="color: var(--rose-500);">*</span></label>
                    <select name="status" id="modalStatusSelect" class="form-control" required>
                        <option value="submitted">Submitted</option>
                        <option value="in_review">In Review</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Resolution / Findings</label>
                    <textarea name="resolution" id="modalResolution" class="form-control" rows="3" placeholder="Action taken or response to resident..."></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeStatusModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Resident Feedback -->
<div id="feedbackModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalFeedbackTitle" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Resident Feedback</h3>
            <button type="button" onclick="closeFeedbackModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="feedbackForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Satisfaction Rating (1 to 5 Stars) <span style="color: var(--rose-500);">*</span></label>
                    <select name="feedback_rating" id="modalRating" class="form-control" required>
                        <option value="5">★★★★★ - Excellent (5 Stars)</option>
                        <option value="4">★★★★☆ - Good (4 Stars)</option>
                        <option value="3">★★★☆☆ - Average (3 Stars)</option>
                        <option value="2">★★☆☆☆ - Poor (2 Stars)</option>
                        <option value="1">★☆☆☆☆ - Very Poor (1 Star)</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Resident Feedback Comments</label>
                    <textarea name="feedback_comments" id="modalComments" class="form-control" rows="3" placeholder="Resident remarks on response quality and time..."></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeFeedbackModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Feedback</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateComplaintModal() {
    document.getElementById('createComplaintModal').style.display = 'flex';
}
function closeCreateComplaintModal() {
    document.getElementById('createComplaintModal').style.display = 'none';
}

function openStatusModal(c) {
    document.getElementById('modalStatusTitle').innerText = 'Update Complaint ' + c.complaint_code;
    document.getElementById('modalStatusSelect').value = c.status;
    document.getElementById('modalResolution').value = c.resolution || '';
    document.getElementById('statusForm').action = '/complaints/status/' + c.id;
    document.getElementById('statusModal').style.display = 'flex';
}
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

function openFeedbackModal(c) {
    document.getElementById('modalFeedbackTitle').innerText = 'Feedback for ' + c.complaint_code;
    document.getElementById('modalRating').value = c.feedback_rating || 5;
    document.getElementById('modalComments').value = c.feedback_comments || '';
    document.getElementById('feedbackForm').action = '/complaints/feedback/' + c.id;
    document.getElementById('feedbackModal').style.display = 'flex';
}
function closeFeedbackModal() {
    document.getElementById('feedbackModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
