<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Customer Communications</span>
        </div>
        <h1 class="page-title">Customer Communication Logs & History</h1>
        <p class="page-subtitle">Track multi-channel client engagements including phone calls, WhatsApp messages, emails, and meetings.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('commModal').style.display='flex'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Log Interaction
        </button>
    </div>
</div>

<!-- Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Engagements</div>
            <div class="metric-value"><?= number_format($totalComms) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">All customer touches</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Phone Calls</div>
            <div class="metric-value" style="color: var(--info);"><?= number_format($callCount) ?></div>
            <div class="metric-meta" style="color: var(--info);">Outbound / inbound</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">WhatsApp Chats</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($waCount) ?></div>
            <div class="metric-meta" style="color: var(--success);">Direct instant messaging</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">In-Person Meetings</div>
            <div class="metric-value" style="color: var(--warning);"><?= number_format($meetCount) ?></div>
            <div class="metric-meta" style="color: var(--warning);">Site visits & desk meetings</div>
        </div>
        <div class="metric-icon-box" style="background: var(--warning-light); color: var(--warning);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/communications" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 100px; gap: 1rem; align-items: flex-end;">
            <div>
                <label class="form-label">Search Query</label>
                <input type="text" name="search" class="form-control" placeholder="Subject, code, notes..." value="<?= esc($search ?? '') ?>">
            </div>
            <div>
                <label class="form-label">Channel</label>
                <select name="channel" class="form-control">
                    <option value="">All Channels</option>
                    <option value="Phone Call" <?= ($channel === 'Phone Call') ? 'selected' : '' ?>>Phone Call</option>
                    <option value="WhatsApp" <?= ($channel === 'WhatsApp') ? 'selected' : '' ?>>WhatsApp</option>
                    <option value="In-Person Meeting" <?= ($channel === 'In-Person Meeting') ? 'selected' : '' ?>>In-Person Meeting</option>
                    <option value="Email" <?= ($channel === 'Email') ? 'selected' : '' ?>>Email</option>
                    <option value="SMS" <?= ($channel === 'SMS') ? 'selected' : '' ?>>SMS</option>
                </select>
            </div>
            <div>
                <label class="form-label">Purpose</label>
                <select name="purpose" class="form-control">
                    <option value="">All Purposes</option>
                    <option value="Site Visit" <?= ($purpose === 'Site Visit') ? 'selected' : '' ?>>Site Visit</option>
                    <option value="Booking Confirmation" <?= ($purpose === 'Booking Confirmation') ? 'selected' : '' ?>>Booking Confirmation</option>
                    <option value="Payment Follow-up" <?= ($purpose === 'Payment Follow-up') ? 'selected' : '' ?>>Payment Follow-up</option>
                    <option value="Document Collection" <?= ($purpose === 'Document Collection') ? 'selected' : '' ?>>Document Collection</option>
                    <option value="Negotiation" <?= ($purpose === 'Negotiation') ? 'selected' : '' ?>>Negotiation</option>
                    <option value="General Inquiry" <?= ($purpose === 'General Inquiry') ? 'selected' : '' ?>>General Inquiry</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                <a href="/communications" class="btn btn-secondary" title="Reset">✕</a>
            </div>
        </form>
    </div>
</div>

<!-- Communications Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Code / Date</th>
                    <th>Contact Party</th>
                    <th>Channel & Purpose</th>
                    <th>Subject & Details</th>
                    <th>Handled By</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($communications)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--slate-500);">
                            No communication logs found matching your filters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($communications as $c): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--primary);"><?= esc($c['comm_code']) ?></strong>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= date('M d, Y h:i A', strtotime($c['communication_date'])) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($c['cust_fname'])): ?>
                                    <div style="font-weight: 500; color: var(--slate-900);"><?= esc($c['cust_fname'] . ' ' . $c['cust_lname']) ?></div>
                                    <span class="badge" style="background: var(--primary-light); color: var(--primary); font-size: 0.7rem;">Customer</span>
                                <?php elseif (!empty($c['lead_fname'])): ?>
                                    <div style="font-weight: 500; color: var(--slate-900);"><?= esc($c['lead_fname'] . ' ' . $c['lead_lname']) ?></div>
                                    <span class="badge" style="background: var(--warning-light); color: var(--warning); font-size: 0.7rem;">Lead</span>
                                <?php else: ?>
                                    <span style="color: var(--slate-400);">General Client</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--slate-800);"><?= esc($c['channel']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($c['purpose']) ?></div>
                            </td>
                            <td style="max-width: 320px;">
                                <div style="font-weight: 600; color: var(--slate-900); font-size: 0.9rem;"><?= esc($c['subject']) ?></div>
                                <div style="font-size: 0.8rem; color: var(--slate-600); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($c['content']) ?></div>
                                <?php if (!empty($c['response_notes'])): ?>
                                    <div style="font-size: 0.75rem; color: var(--success); margin-top: 0.2rem;">&rsaquo; Outcome: <?= esc($c['response_notes']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--slate-700);"><?= esc($c['staff_name'] ?? 'System') ?></span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $c['status'] === 'Completed' ? 'success' : ($c['status'] === 'Pending' ? 'warning' : 'secondary') ?>">
                                    <?= esc($c['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Log Communication -->
<div id="commModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 650px; border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 600;">Log Client Interaction</h3>
            <button type="button" onclick="document.getElementById('commModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form action="/communications/store" method="POST">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; max-height: calc(85vh - 140px); overflow-y: auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label">Associate Customer</label>
                        <select name="customer_id" class="form-control">
                            <option value="">-- None / Select Customer --</option>
                            <?php foreach ($customers as $cust): ?>
                                <option value="<?= $cust['id'] ?>"><?= esc($cust['first_name'] . ' ' . $cust['last_name']) ?> (<?= esc($cust['phone'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Or Associate Lead</label>
                        <select name="lead_id" class="form-control">
                            <option value="">-- None / Select Lead --</option>
                            <?php foreach ($leads as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= esc($l['first_name'] . ' ' . $l['last_name']) ?> (<?= esc($l['phone'] ?? '') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label">Communication Channel <span style="color: var(--danger);">*</span></label>
                        <select name="channel" class="form-control" required>
                            <option value="Phone Call">Phone Call</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="In-Person Meeting">In-Person Meeting</option>
                            <option value="Email">Email</option>
                            <option value="SMS">SMS</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Purpose / Context <span style="color: var(--danger);">*</span></label>
                        <select name="purpose" class="form-control" required>
                            <option value="Site Visit">Site Visit</option>
                            <option value="Booking Confirmation">Booking Confirmation</option>
                            <option value="Payment Follow-up">Payment Follow-up</option>
                            <option value="Document Collection">Document Collection</option>
                            <option value="Negotiation">Negotiation</option>
                            <option value="General Inquiry">General Inquiry</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label">Subject / Topic <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="subject" class="form-control" required placeholder="e.g. Discussed Tower B 3BHK Price Quote">
                    </div>
                    <div>
                        <label class="form-label">Date & Time <span style="color: var(--danger);">*</span></label>
                        <input type="datetime-local" name="communication_date" class="form-control" required value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                </div>

                <div>
                    <label class="form-label">Summary / Notes <span style="color: var(--danger);">*</span></label>
                    <textarea name="content" class="form-control" rows="3" required placeholder="Summary of client queries, commitments, or talking points..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Completed" selected>Completed</option>
                            <option value="Pending">Pending Follow-up</option>
                            <option value="No Answer">No Answer / Left Voicemail</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Follow-up Outcome / Next Step</label>
                        <input type="text" name="response_notes" class="form-control" placeholder="e.g. Agreed to visit showroom on Saturday">
                    </div>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; background: var(--slate-50); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('commModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Interaction</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
