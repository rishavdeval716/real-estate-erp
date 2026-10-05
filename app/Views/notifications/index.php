<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Notification & Reminders</span>
        </div>
        <h1 class="page-title">Notification & Reminder Center</h1>
        <p class="page-subtitle">Centralized broadcast, lease renewal alarms, payment due reminders, and operational alerts.</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/notifications/mark-all-read" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><polyline points="20 6 9 17 4 12"/></svg>
            Mark All as Read
        </a>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('broadcastModal').style.display='flex'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><line x1="12" y1="2" x2="12" y2="4"/></svg>
            New Reminder Broadcast
        </button>
    </div>
</div>

<!-- Quick Metrics -->
<div class="metric-grid" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Unread Alerts</div>
            <div class="metric-value" style="color: <?= $unreadCount > 0 ? 'var(--danger)' : 'var(--success)' ?>;"><?= $unreadCount ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Requires immediate review</div>
        </div>
        <div class="metric-icon-box" style="background: <?= $unreadCount > 0 ? 'var(--danger-light)' : 'var(--success-light)' ?>; color: <?= $unreadCount > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Notifications</div>
            <div class="metric-value"><?= count($notifications) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">System-wide logs & dispatches</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="13" y2="16"/></svg>
        </div>
    </div>
    <div class="metric-card">
        <div>
            <div class="metric-label">Filter Active</div>
            <div class="metric-value" style="font-size: 1.25rem; text-transform: capitalize;"><?= $filter ? esc($filter) : 'All Activity' ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Displaying filtered stream</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        </div>
    </div>
</div>

<!-- Tabs & Actions -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--border-color); flex-wrap: wrap;">
        <a href="/notifications" class="btn <?= empty($filter) ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
            All Notifications (<?= count($notifications) ?>)
        </a>
        <a href="/notifications?filter=unread" class="btn <?= $filter === 'unread' ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
            Unread Only (<?= $unreadCount ?>)
        </a>
        <a href="/notifications?filter=urgent" class="btn <?= $filter === 'urgent' ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
            High / Urgent Priority
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($notifications)): ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--slate-500);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 1rem; opacity: 0.4;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <h4 style="font-size: 1.1rem; color: var(--slate-700); margin-bottom: 0.3rem;">No notifications found</h4>
                <p style="font-size: 0.875rem;">You are all caught up! There are no alerts matching your criteria.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column;">
                <?php foreach ($notifications as $n): ?>
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; background: <?= empty($n['is_read']) ? 'rgba(59, 130, 246, 0.03)' : 'transparent' ?>; transition: background 0.2s;" onmouseover="this.style.background='var(--slate-50)'" onmouseout="this.style.background='<?= empty($n['is_read']) ? 'rgba(59, 130, 246, 0.03)' : 'transparent' ?>'">
                        <div style="display: flex; gap: 1rem; align-items: flex-start;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: <?= $n['priority'] === 'urgent' ? 'var(--danger-light)' : ($n['priority'] === 'high' ? 'var(--warning-light)' : 'var(--primary-light)') ?>; color: <?= $n['priority'] === 'urgent' ? 'var(--danger)' : ($n['priority'] === 'high' ? 'var(--warning)' : 'var(--primary)') ?>;">
                                <?php if ($n['type'] === 'lead'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                <?php elseif ($n['type'] === 'payment'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <?php elseif ($n['type'] === 'lease'): ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <?php else: ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.25rem; flex-wrap: wrap;">
                                    <h4 style="font-size: 1rem; font-weight: 600; color: var(--slate-900); margin: 0;"><?= esc($n['title']) ?></h4>
                                    <?php if (empty($n['is_read'])): ?>
                                        <span class="badge" style="background: var(--primary); color: white; font-size: 0.7rem; padding: 0.15rem 0.4rem;">NEW</span>
                                    <?php endif; ?>
                                    <span class="badge badge-<?= $n['priority'] === 'urgent' ? 'danger' : ($n['priority'] === 'high' ? 'warning' : 'secondary') ?>" style="font-size: 0.7rem; text-transform: uppercase;">
                                        <?= esc($n['priority']) ?>
                                    </span>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-600); font-size: 0.7rem; text-transform: capitalize;">
                                        <?= esc($n['type']) ?>
                                    </span>
                                </div>
                                <p style="font-size: 0.875rem; color: var(--slate-600); margin: 0 0 0.5rem 0; line-height: 1.4;"><?= esc($n['message']) ?></p>
                                <div style="font-size: 0.75rem; color: var(--slate-400);">
                                    <?= date('M d, Y h:i A', strtotime($n['created_at'])) ?>
                                    <?php if ($n['is_read'] && !empty($n['read_at'])): ?>
                                        &bull; Read on <?= date('M d, Y h:i A', strtotime($n['read_at'])) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0; display: flex; gap: 0.5rem; align-items: center;">
                            <?php if (empty($n['is_read'])): ?>
                                <a href="/notifications/mark-read/<?= $n['id'] ?>" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                    Mark Read
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($n['action_url'])): ?>
                                <a href="<?= esc($n['action_url']) ?>" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                                    View
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Broadcast Reminder Modal -->
<div id="broadcastModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 550px; border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 600;">Broadcast Internal Reminder</h3>
            <button type="button" onclick="document.getElementById('broadcastModal').style.display='none'" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form action="/notifications/create" method="POST">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label class="form-label" style="font-size: 0.875rem; font-weight: 500;">Reminder Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Q4 Lease Renewal Follow-up Notice">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.875rem; font-weight: 500;">Category / Module</label>
                        <select name="type" class="form-control">
                            <option value="system">General / System</option>
                            <option value="lead">Lead / CRM Alert</option>
                            <option value="payment">Payment & Receivables</option>
                            <option value="lease">Lease & Agreement</option>
                            <option value="maintenance">Maintenance Ticket</option>
                            <option value="verification">Legal & Compliance</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.875rem; font-weight: 500;">Priority Level</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.875rem; font-weight: 500;">Message Content <span style="color: var(--danger);">*</span></label>
                    <textarea name="message" class="form-control" rows="3" required placeholder="Provide clear instructions or reminders for staff members..."></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; background: var(--slate-50); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('broadcastModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Dispatch Reminder</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
