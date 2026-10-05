<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>System Settings</span>
        </div>
        <h1 class="page-title">Settings & System Configuration</h1>
        <p class="page-subtitle">Configure enterprise parameters, statutory tax rates, default currencies, document policies, and data export.</p>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body" style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding: 0.75rem 1rem;">
        <a href="/settings?tab=general" class="btn <?= ($activeTab === 'general') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            General & Localization
        </a>
        <a href="/settings?tab=company" class="btn <?= ($activeTab === 'company') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Company & Entity
        </a>
        <a href="/settings?tab=property" class="btn <?= ($activeTab === 'property') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Property & Inventory
        </a>
        <a href="/settings?tab=payment" class="btn <?= ($activeTab === 'payment') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Tax & Payments
        </a>
        <a href="/settings?tab=notification" class="btn <?= ($activeTab === 'notification') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Notifications & Alerts
        </a>
        <a href="/settings?tab=document" class="btn <?= ($activeTab === 'document') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Document Security
        </a>
        <a href="/settings?tab=backup" class="btn <?= ($activeTab === 'backup') ? 'btn-primary' : 'btn-secondary' ?>" style="font-size: 0.85rem;">
            Backup & Data Export
        </a>
    </div>
</div>

<?php if ($activeTab === 'backup'): ?>
    <!-- Backup and Data Export Hub -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        <!-- Database Backup Card -->
        <div class="card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">System Database Backup</h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1.5rem;">
                    Trigger a point-in-time snapshot of the MySQL database. An immutable archive entry will be written to the system audit trail.
                </p>
                <form action="/settings/backup" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Generate an immediate database backup archive?')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Generate Database Snapshot
                    </button>
                </form>
            </div>
        </div>

        <!-- Data Export Card -->
        <div class="card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Data Export (JSON Archive)</h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                    Export active datasets in structured JSON format for compliance audits, migrations, or data analytics:
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="/settings/export?type=properties" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Properties & Units</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=leads" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>CRM Leads & Inquiries</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=bookings" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Sales Bookings</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=payments" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Payment Transactions</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=expenses" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Property Expenses</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=owners" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Property Owners</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                    <a href="/settings/export?type=agents" class="btn btn-secondary" style="justify-content: space-between;">
                        <span>Agents & Brokers</span>
                        <span style="font-size: 0.75rem; color: var(--primary);">Export JSON &rsaquo;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Tab Settings Form -->
    <div class="card">
        <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; text-transform: capitalize;">
                <?= esc($activeTab) ?> Configuration
            </h3>
        </div>
        <form action="/settings/update" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="tab" value="<?= esc($activeTab) ?>">

            <div class="card-body" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <?php
                $currentGroupSettings = $groupedSettings[$activeTab] ?? [];
                ?>
                <?php if (empty($currentGroupSettings)): ?>
                    <p style="color: var(--slate-500); padding: 1rem 0;">No configurable items found for this tab.</p>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($currentGroupSettings as $s): ?>
                            <div>
                                <label class="form-label" style="font-weight: 600; font-size: 0.875rem;">
                                    <?= esc(ucwords(str_replace('_', ' ', $s['setting_key']))) ?>
                                </label>
                                <?php if ($s['setting_type'] === 'textarea'): ?>
                                    <textarea name="settings[<?= esc($s['setting_key']) ?>]" class="form-control" rows="3"><?= esc($s['setting_value']) ?></textarea>
                                <?php elseif ($s['setting_type'] === 'number'): ?>
                                    <input type="number" step="any" name="settings[<?= esc($s['setting_key']) ?>]" class="form-control" value="<?= esc($s['setting_value']) ?>">
                                <?php elseif ($s['setting_type'] === 'boolean'): ?>
                                    <select name="settings[<?= esc($s['setting_key']) ?>]" class="form-control">
                                        <option value="1" <?= ($s['setting_value'] == '1' || $s['setting_value'] == 'true') ? 'selected' : '' ?>>Enabled / Yes</option>
                                        <option value="0" <?= ($s['setting_value'] == '0' || $s['setting_value'] == 'false') ? 'selected' : '' ?>>Disabled / No</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" name="settings[<?= esc($s['setting_key']) ?>]" class="form-control" value="<?= esc($s['setting_value']) ?>">
                                <?php endif; ?>
                                <?php if (!empty($s['description'])): ?>
                                    <small style="color: var(--slate-500); font-size: 0.75rem; display: block; margin-top: 0.25rem;"><?= esc($s['description']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-footer" style="padding: 1rem 1.5rem; background: var(--slate-50); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.3rem;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
