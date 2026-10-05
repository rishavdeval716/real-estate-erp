<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Lead Profile Header Card -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(to right, #ffffff, #f8fafc); border-left: 4px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div class="breadcrumb" style="margin-bottom: 0.5rem;">
                <a href="/dashboard">Dashboard</a> &rsaquo;
                <a href="/leads">CRM Leads</a> &rsaquo;
                <span><?= esc($lead['lead_code']) ?></span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--slate-900); margin: 0;">
                    <?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?>
                </h1>
                <span style="font-family: monospace; font-size: 0.85rem; background: var(--slate-100); padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid var(--slate-200);">
                    <?= esc($lead['lead_code']) ?>
                </span>
                <?= \App\Libraries\LeadStatus::renderStatusBadge($lead['lead_status']) ?>
                <?= \App\Libraries\LeadStatus::renderStageBadge($lead['lead_stage']) ?>
                <?= \App\Libraries\LeadStatus::renderPriorityBadge($lead['priority']) ?>
            </div>

            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.88rem; color: var(--slate-600);">
                <div>
                    <strong>Phone:</strong> 
                    <a href="tel:<?= esc($lead['phone']) ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                        <?= esc($lead['phone']) ?>
                    </a>
                </div>
                <?php if ($lead['email']): ?>
                <div>
                    <strong>Email:</strong> 
                    <a href="mailto:<?= esc($lead['email']) ?>" style="color: var(--primary); text-decoration: none;">
                        <?= esc($lead['email']) ?>
                    </a>
                </div>
                <?php endif; ?>
                <div>
                    <strong>Source:</strong> <?= esc($lead['source_name'] ?: 'Direct') ?>
                </div>
                <div>
                    <strong>Assigned:</strong> 
                    <span style="font-weight: 600; color: var(--slate-800);"><?= esc($lead['assigned_to_name'] ?: 'Unassigned') ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
            <a href="/leads/edit/<?= esc($lead['id']) ?>" class="btn btn-secondary">Edit Lead</a>
            <button type="button" class="btn btn-secondary" onclick="openModal('modal-reassign')">Reassign</button>
            <button type="button" class="btn btn-secondary" onclick="openModal('modal-stage')">Move Stage</button>
            <button type="button" class="btn btn-primary" onclick="openModal('modal-followup')">+ Follow-up</button>
            <button type="button" class="btn btn-secondary" onclick="openModal('modal-sitevisit')">+ Site Visit</button>
            <button type="button" class="btn btn-secondary" style="background: #fdf4ff; border-color: #f5d0fe; color: #a21caf;" onclick="openModal('modal-hold')">+ Unit Hold</button>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="tabs-nav" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--slate-200); margin-bottom: 1.5rem; overflow-x: auto;">
    <button type="button" class="tab-btn active" onclick="switchTab(event, 'tab-overview')">Overview & Profile</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-interests')">Interested Properties (<?= count($interests) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-enquiries')">Enquiries (<?= count($enquiries) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-followups')">Follow-ups (<?= count($followups) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-visits')">Site Visits (<?= count($visits) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-holds')">Unit Holds (<?= count($holds) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-timeline')">Activity Timeline (<?= count($timeline) ?>)</button>
</div>

<!-- TAB 1: OVERVIEW & PROSPECT DEMOGRAPHIC -->
<div id="tab-overview" class="tab-pane active">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
        
        <!-- Requirement Specifications -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                Requirement Specifications
            </h3>
            <table class="table" style="margin: 0;">
                <tbody>
                    <tr>
                        <td style="width: 40%; color: var(--slate-500); font-weight: 600;">Budget Range</td>
                        <td style="font-weight: 700; color: var(--slate-900);">
                            <?php if ((float)$lead['budget_max'] > 0): ?>
                                ₹<?= (float)$lead['budget_min'] > 0 ? number_format((float)$lead['budget_min'], 2) . ' — ' : '' ?>
                                ₹<?= number_format((float)$lead['budget_max'], 2) ?>
                            <?php else: ?>
                                <span style="color: var(--slate-400);">Not specified</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Preferred Category</td>
                        <td><?= esc($lead['property_type_name'] ?: 'Any Category') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Preferred Location</td>
                        <td><?= esc($lead['preferred_location'] ?: 'Not specified') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Target Project</td>
                        <td><?= esc($lead['project_name'] ?: 'None selected') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Purchase Purpose</td>
                        <td><?= esc($lead['purchase_purpose'] ?: 'End Use') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Purchase Timeline</td>
                        <td><?= esc($lead['purchase_timeline'] ?: 'Immediate') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Financing Needed</td>
                        <td>
                            <span style="font-weight: 600; color: <?= $lead['financing_required'] === 'Yes' ? 'var(--primary)' : 'var(--slate-700)' ?>;">
                                <?= esc($lead['financing_required']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Site Visit Status</td>
                        <td><?= esc($lead['site_visit_required']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Intake Remarks</td>
                        <td style="color: var(--slate-700); font-style: italic;"><?= esc($lead['remarks'] ?: 'No intake notes.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Prospect Profile -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
                Customer / Prospect Demographic
            </h3>
            <?php if ($prospect): ?>
                <table class="table" style="margin: 0;">
                    <tbody>
                        <tr>
                            <td style="width: 40%; color: var(--slate-500); font-weight: 600;">Full Legal Name</td>
                            <td style="font-weight: 600; color: var(--slate-900);"><?= esc($prospect['name']) ?></td>
                        </tr>
                        <tr>
                            <td style="color: var(--slate-500); font-weight: 600;">Occupation / Profession</td>
                            <td><?= esc($prospect['occupation'] ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td style="color: var(--slate-500); font-weight: 600;">Preferred Contact Mode</td>
                            <td>
                                <span style="background: var(--slate-100); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    <?= esc($prospect['preferred_contact_method']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="color: var(--slate-500); font-weight: 600;">Postal Address</td>
                            <td><?= esc($prospect['address'] ?: '—') ?></td>
                        </tr>
                        <tr>
                            <td style="color: var(--slate-500); font-weight: 600;">City / State / PIN</td>
                            <td>
                                <?= esc(trim(($prospect['city'] ? $prospect['city'] . ', ' : '') . ($prospect['state'] ? $prospect['state'] . ' ' : '') . ($prospect['pincode'] ?? ''))) ?: '—' ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="color: var(--slate-500); font-weight: 600;">Profile Notes</td>
                            <td style="color: var(--slate-600);"><?= esc($prospect['notes'] ?: '—') ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="color: var(--slate-400); padding: 1.5rem; text-align: center;">
                    No demographic profile logged yet.
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- TAB 2: INTERESTED PROPERTIES -->
<div id="tab-interests" class="tab-pane" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin: 0;">
                Shortlisted / Interested Properties & Units
            </h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-interest')">+ Add Property Interest</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Property</th>
                        <th>Specific Unit</th>
                        <th>Level</th>
                        <th>Remarks</th>
                        <th>Logged Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($interests)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                No specific properties shortlisted yet for this lead.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($interests as $in): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= esc($in['project_name'] ?: '—') ?></td>
                                <td>
                                    <?php if (!empty($in['property_title'])): ?>
                                        <div style="font-weight: 600; color: var(--primary);"><?= esc($in['property_title']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($in['property_code']) ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400);">Project-level</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($in['unit_number'])): ?>
                                        <code><?= esc($in['unit_number']) ?></code>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400);">Any unit</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8;">
                                        <?= esc($in['interest_level']) ?>
                                    </span>
                                </td>
                                <td style="color: var(--slate-600);"><?= esc($in['remarks'] ?: '—') ?></td>
                                <td style="font-size: 0.85rem; color: var(--slate-500);"><?= date('d M Y', strtotime($in['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: ENQUIRIES -->
<div id="tab-enquiries" class="tab-pane" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin: 0;">
                Logged Formal Enquiries
            </h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-enquiry')">+ Log New Enquiry</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Enquiry Code</th>
                        <th>Type</th>
                        <th>Property / Project</th>
                        <th>Budget</th>
                        <th>Requirement</th>
                        <th>Status</th>
                        <th>Logged By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enquiries)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                No formal enquiries logged for this lead yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enquiries as $enq): ?>
                            <tr>
                                <td><code><?= esc($enq['enquiry_code']) ?></code></td>
                                <td><span style="font-weight: 600;"><?= esc($enq['enquiry_type']) ?></span></td>
                                <td><?= esc($enq['property_title'] ?: ($enq['project_name'] ?: 'General Portfolio')) ?></td>
                                <td><?= (float)$enq['budget'] > 0 ? '₹' . number_format((float)$enq['budget'], 2) : '—' ?></td>
                                <td style="max-width: 250px; color: var(--slate-700);"><?= esc($enq['requirement'] ?: '—') ?></td>
                                <td>
                                    <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #faf5ff; color: #7e22ce;">
                                        <?= esc($enq['status']) ?>
                                    </span>
                                </td>
                                <td><?= esc($enq['creator_name'] ?: 'Sales Agent') ?></td>
                                <td style="font-size: 0.85rem; color: var(--slate-500);"><?= date('d M Y', strtotime($enq['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 4: FOLLOW-UPS -->
<div id="tab-followups" class="tab-pane" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin: 0;">
                Scheduled & Completed Follow-ups
            </h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-followup')">+ Schedule Follow-up</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Channel</th>
                        <th>Scheduled For</th>
                        <th>Assigned Executive</th>
                        <th>Status</th>
                        <th>Notes / Outcome</th>
                        <th>Completed At</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($followups)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                No follow-up activities recorded yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($followups as $fu): ?>
                            <tr>
                                <td>
                                    <span style="font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <?= esc($fu['followup_type']) ?>
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--slate-900);">
                                    <?= date('d M Y, h:i A', strtotime($fu['scheduled_at'])) ?>
                                </td>
                                <td><?= esc($fu['assigned_to_name']) ?></td>
                                <td>
                                    <?php if ($fu['status'] === 'Completed'): ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #047857;">Completed</span>
                                    <?php elseif ($fu['status'] === 'Cancelled'): ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #fef2f2; color: #b91c1c;">Cancelled</span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8;">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 250px;">
                                    <div><?= esc($fu['notes'] ?: '—') ?></div>
                                    <?php if ($fu['outcome']): ?>
                                        <div style="font-size: 0.75rem; color: var(--success); font-weight: 600;">Outcome: <?= esc($fu['outcome']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--slate-500);">
                                    <?= $fu['completed_at'] ? date('d M Y, h:i A', strtotime($fu['completed_at'])) : '—' ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($fu['status'] === 'Pending'): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCompleteFollowupModal(<?= esc($fu['id']) ?>)">Complete</button>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.8rem;">Archived</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 5: SITE VISITS -->
<div id="tab-visits" class="tab-pane" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin: 0;">
                Property & Project Site Visits
            </h3>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-sitevisit')">+ Book Site Visit</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Visit Code</th>
                        <th>Scheduled For</th>
                        <th>Visit Type</th>
                        <th>Target Location / Property</th>
                        <th>Agent</th>
                        <th>Status</th>
                        <th>Rating / Feedback</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visits)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                No site visits scheduled or completed yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($visits as $sv): ?>
                            <tr>
                                <td><code><?= esc($sv['visit_code']) ?></code></td>
                                <td style="font-weight: 600; color: var(--slate-900);">
                                    <?= date('d M Y, h:i A', strtotime($sv['scheduled_at'])) ?>
                                </td>
                                <td><?= esc($sv['visit_type']) ?></td>
                                <td><?= esc($sv['property_title'] ?: ($sv['project_name'] ?: 'General Site')) ?></td>
                                <td><?= esc($sv['assigned_agent_name'] ?: 'Agent') ?></td>
                                <td>
                                    <?php if ($sv['status'] === 'Completed'): ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #047857;">Completed</span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #fffbeb; color: #b45309;">
                                            <?= esc($sv['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sv['status'] === 'Completed'): ?>
                                        <div>
                                            <strong style="color: #f59e0b;">★ <?= esc($sv['rating'] ?: '5') ?>/5</strong> 
                                            (<?= esc($sv['interest_level'] ?: 'High') ?>)
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--slate-600);"><?= esc($sv['feedback'] ?: 'No notes') ?></div>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400);">Pending visit</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($sv['status'] !== 'Completed' && $sv['status'] !== 'Cancelled'): ?>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCompleteVisitModal(<?= esc($sv['id']) ?>)">Log Feedback</button>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.8rem;">Done</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 6: UNIT HOLDS -->
<div id="tab-holds" class="tab-pane" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin: 0;">
                    Temporary Reservation / Unit Holds
                </h3>
                <p style="font-size: 0.82rem; color: var(--slate-500); margin: 0.2rem 0 0 0;">
                    Holds lock available units into Reserved status while token advance & paperwork are underway.
                </p>
            </div>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-hold')">+ Place Unit Hold</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Hold Code</th>
                        <th>Target Unit</th>
                        <th>Property</th>
                        <th>Held By</th>
                        <th>Started At</th>
                        <th>Expires At</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($holds)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                No temporary holds placed for this lead.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($holds as $h): ?>
                            <tr>
                                <td><code><?= esc($h['hold_code']) ?></code></td>
                                <td style="font-weight: 700; color: var(--primary);">
                                    Unit <?= esc($h['unit_number']) ?> (<?= esc($h['flat_type']) ?>)
                                </td>
                                <td><?= esc($h['property_title'] ?: 'Standalone') ?></td>
                                <td><?= esc($h['held_by_name'] ?: 'Manager') ?></td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y, h:i A', strtotime($h['started_at'])) ?></td>
                                <td style="font-size: 0.85rem; font-weight: 600; color: var(--danger);">
                                    <?= date('d M Y, h:i A', strtotime($h['expires_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($h['hold_status'] === 'Active'): ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">Active Hold</span>
                                    <?php elseif ($h['hold_status'] === 'Released'): ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Released</span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;"><?= esc($h['hold_status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($h['hold_status'] === 'Active'): ?>
                                        <form method="post" action="/unit-holds/release/<?= esc($h['id']) ?>" style="display: inline;" onsubmit="return confirm('Release this unit hold? The unit will return to Available status.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">Release Hold</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--slate-400); font-size: 0.8rem;">Archived</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 7: ACTIVITY TIMELINE -->
<div id="tab-timeline" class="tab-pane" style="display: none;">
    <div class="card">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--slate-900); margin-bottom: 1.5rem; border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem;">
            Unified CRM Activity Timeline
        </h3>

        <div style="position: relative; padding-left: 2rem;">
            <!-- Timeline vertical connector line -->
            <div style="position: absolute; left: 0.75rem; top: 0.5rem; bottom: 0.5rem; width: 2px; background: var(--slate-200);"></div>

            <?php foreach ($timeline as $t): ?>
                <div style="position: relative; margin-bottom: 1.5rem;">
                    <!-- Dot -->
                    <div style="position: absolute; left: -1.65rem; top: 0.25rem; width: 14px; height: 14px; border-radius: 50%; background: var(--primary); border: 3px solid #fff; box-shadow: 0 0 0 1px var(--primary);"></div>
                    
                    <div style="background: #f8fafc; border: 1px solid var(--slate-200); border-radius: 8px; padding: 0.85rem 1.15rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; flex-wrap: wrap;">
                            <span style="font-weight: 700; color: var(--slate-900); font-size: 0.95rem;">
                                <?= esc($t['title']) ?>
                            </span>
                            <span style="font-size: 0.75rem; color: var(--slate-500);">
                                <?= date('d M Y, h:i A', strtotime($t['datetime'])) ?>
                            </span>
                        </div>
                        <div style="font-size: 0.88rem; color: var(--slate-700); margin-bottom: 0.4rem;">
                            <?= esc($t['description']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--slate-500);">
                            Actor: <strong><?= esc($t['user']) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODALS -->
<!-- ========================================== -->

<!-- Modal: Reassign Lead -->
<div id="modal-reassign" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Assign / Reassign Lead</h3>
        <form method="post" action="/leads/assign/<?= esc($lead['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Select Sales Executive</label>
                <select name="assigned_user_id" class="form-control" required>
                    <option value="">Select Executive</option>
                    <?php foreach ($executives as $ex): ?>
                        <option value="<?= esc($ex['id']) ?>" <?= $lead['assigned_user_id'] == $ex['id'] ? 'selected' : '' ?>><?= esc($ex['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Reason for assignment / handover notes..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-reassign')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Change Stage -->
<div id="modal-stage" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Advance Pipeline Stage</h3>
        <form method="post" action="/leads/stage/<?= esc($lead['id']) ?>">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Target Stage</label>
                <select name="lead_stage" class="form-control" required>
                    <?php foreach ($stages as $sg): ?>
                        <option value="<?= esc($sg) ?>" <?= $lead['lead_stage'] === $sg ? 'selected' : '' ?>><?= esc($sg) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2" placeholder="Stage transition notes..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-stage')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Stage</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Schedule Follow-up -->
<div id="modal-followup" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Schedule Follow-up Task</h3>
        <form method="post" action="/followups/store">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" value="<?= esc($lead['id']) ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Channel</label>
                    <select name="followup_type" class="form-control" required>
                        <option value="Phone Call">Phone Call</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="Email">Email</option>
                        <option value="Meeting">In-Person Meeting</option>
                        <option value="Site Visit">Site Visit Coordination</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+1 day 10:00')) ?>">
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Discussion topics, payment review, brochure request..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-followup')">Cancel</button>
                <button type="submit" class="btn btn-primary">Schedule Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Complete Follow-up -->
<div id="modal-complete-followup" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 480px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Complete Follow-up</h3>
        <form id="form-complete-followup" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Call / Meeting Outcome</label>
                <input type="text" name="outcome" class="form-control" required placeholder="e.g. Client interested in 3 BHK, scheduled site visit">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Additional Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Detailed conversation highlights..."></textarea>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Next Follow-up (Optional)</label>
                <input type="datetime-local" name="next_followup_at" class="form-control">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-complete-followup')">Cancel</button>
                <button type="submit" class="btn btn-primary">Mark Completed</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Schedule Site Visit -->
<div id="modal-sitevisit" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Book Site Visit</h3>
        <form method="post" action="/site-visits/store">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" value="<?= esc($lead['id']) ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Visit Type</label>
                    <select name="visit_type" class="form-control">
                        <option value="Property Visit">Property Visit</option>
                        <option value="Project Visit">Project Visit</option>
                        <option value="Virtual Visit">Virtual Video Walkthrough</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+2 days 11:00')) ?>">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $prj): ?>
                            <option value="<?= esc($prj['id']) ?>" <?= $lead['project_id'] == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Property</label>
                    <select name="property_id" class="form-control">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?= esc($prop['id']) ?>" <?= $lead['property_id'] == $prop['id'] ? 'selected' : '' ?>><?= esc($prop['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Visitor Count & Notes</label>
                <input type="number" name="visitor_count" class="form-control" value="2" placeholder="e.g. 2 visitors">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-sitevisit')">Cancel</button>
                <button type="submit" class="btn btn-primary">Book Visit</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Complete Site Visit -->
<div id="modal-complete-visit" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Record Site Visit Feedback</h3>
        <form id="form-complete-visit" method="post" action="">
            <?= csrf_field() ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Client Rating</label>
                    <select name="rating" class="form-control" required>
                        <option value="5">★★★★★ (5/5 Excellent)</option>
                        <option value="4">★★★★☆ (4/5 Very Good)</option>
                        <option value="3">★★★☆☆ (3/5 Average)</option>
                        <option value="2">★★☆☆☆ (2/5 Below Expectation)</option>
                        <option value="1">★☆☆☆☆ (1/5 Poor)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Interest Level</label>
                    <select name="interest_level" class="form-control" required>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                        <option value="Not Interested">Not Interested</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Client Feedback</label>
                <textarea name="feedback" class="form-control" rows="2" placeholder="Client comments about carpet area, amenities, light..."></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">Preferred Unit</label>
                    <input type="text" name="preferred_unit" class="form-control" placeholder="e.g. Unit A-102">
                </div>
                <div class="form-group">
                    <label class="form-label">Next Action</label>
                    <select name="next_action" class="form-control">
                        <option value="Negotiation">Negotiation</option>
                        <option value="Token Discussion">Token Discussion</option>
                        <option value="Follow-up">Follow-up Call</option>
                        <option value="Alternative Property">Alternative Property</option>
                        <option value="Lost">Lost</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-complete-visit')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Feedback</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Unit Hold -->
<div id="modal-hold" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Temporary Unit Hold</h3>
        <p style="font-size: 0.82rem; color: var(--slate-500); margin: 0 0 1rem 0;">
            Places a temporary hold on an available unit. Unit status will transition from Available to Reserved.
        </p>
        <form method="post" action="/unit-holds/store">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" value="<?= esc($lead['id']) ?>">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Select Available Unit <span style="color: var(--danger);">*</span></label>
                <select name="property_unit_id" class="form-control" required>
                    <option value="">Select Available Unit</option>
                    <?php foreach ($units as $u): ?>
                        <option value="<?= esc($u['id']) ?>">Unit <?= esc($u['unit_number']) ?> - <?= esc($u['flat_type']) ?> (₹<?= number_format((float)$u['unit_price']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Hold Duration</label>
                    <select name="duration_hours" class="form-control">
                        <option value="24">24 Hours</option>
                        <option value="48" selected>48 Hours (Standard)</option>
                        <option value="72">72 Hours (3 Days)</option>
                        <option value="168">7 Days (Manager Approval)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Hold Reason</label>
                    <input type="text" name="hold_reason" class="form-control" placeholder="Token discussion / KYC review">
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-hold')">Cancel</button>
                <button type="submit" class="btn btn-primary">Place Hold</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Log Enquiry -->
<div id="modal-enquiry" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Log Property Enquiry</h3>
        <form method="post" action="/enquiries/store">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" value="<?= esc($lead['id']) ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Enquiry Type</label>
                    <select name="enquiry_type" class="form-control">
                        <option value="Purchase">Purchase</option>
                        <option value="Investment">Investment</option>
                        <option value="Rent">Rent</option>
                        <option value="Commercial">Commercial</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $prj): ?>
                            <option value="<?= esc($prj['id']) ?>" <?= $lead['project_id'] == $prj['id'] ? 'selected' : '' ?>><?= esc($prj['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Specific Requirement Notes</label>
                <textarea name="requirement" class="form-control" rows="2" placeholder="e.g. Higher floor, corner flat, 2 covered parking spaces..."></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-enquiry')">Cancel</button>
                <button type="submit" class="btn btn-primary">Log Enquiry</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Property Interest -->
<div id="modal-interest" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 500px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Add Property Interest</h3>
        <form method="post" action="/leads/interest/<?= esc($lead['id']) ?>">
            <?= csrf_field() ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $prj): ?>
                            <option value="<?= esc($prj['id']) ?>"><?= esc($prj['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Property</label>
                    <select name="property_id" class="form-control">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?= esc($prop['id']) ?>"><?= esc($prop['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Interest Level</label>
                <select name="interest_level" class="form-control">
                    <option value="Primary">Primary (First Preference)</option>
                    <option value="Interested" selected>Interested</option>
                    <option value="Alternative">Alternative Option</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-interest')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Interest</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(evt, tabId) {
    document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    evt.currentTarget.classList.add('active');
}

function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}

function openCompleteFollowupModal(id) {
    document.getElementById('form-complete-followup').action = '/followups/complete/' + id;
    openModal('modal-complete-followup');
}

function openCompleteVisitModal(id) {
    document.getElementById('form-complete-visit').action = '/site-visits/complete/' + id;
    openModal('modal-complete-visit');
}
</script>

<?= $this->endSection() ?>
