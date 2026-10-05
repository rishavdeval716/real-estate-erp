<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/campaigns">Marketing Campaigns</a> &rsaquo;
            <span>Launch Campaign</span>
        </div>
        <h1 class="page-title">Launch Marketing Campaign</h1>
        <p class="page-subtitle">Configure promotional campaign parameters, assign budgets, and bind target inventory.</p>
    </div>
</div>

<div class="card" style="max-width: 850px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Campaign Configuration</h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.25rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="/campaigns/store">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Campaign Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Prestige Skyrise Festive Launch - Meta Ads" value="<?= old('name') ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign Channel Type <span style="color: var(--danger);">*</span></label>
                        <select name="campaign_type" class="form-control" required>
                            <option value="Social Media">Social Media (Meta / Instagram)</option>
                            <option value="Google Ads">Google Ads / SEM</option>
                            <option value="Property Portal">Property Portal (MagicBricks / 99acres)</option>
                            <option value="Hoarding / Outdoor">Outdoor Billboards / Hoardings</option>
                            <option value="Print Media">Print Media / Newspapers</option>
                            <option value="Email Marketing">Email Marketing Blast</option>
                            <option value="SMS Campaign">SMS / WhatsApp Broadcast</option>
                            <option value="Event / Expo">Property Expo / Real Estate Fair</option>
                            <option value="Other">Other Acquisition Channel</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Target Project (Optional)</label>
                        <select name="project_id" class="form-control">
                            <option value="">— Select Target Project —</option>
                            <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>"><?= esc($proj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign Start Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="start_date" class="form-control" required value="<?= old('start_date', date('Y-m-d')) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= old('end_date') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Allocated Budget (₹) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" name="budget" class="form-control" required placeholder="0.00" value="<?= old('budget') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Initial Actual Spend (₹)</label>
                        <input type="number" step="0.01" name="actual_spend" class="form-control" value="<?= old('actual_spend', '0.00') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Leads Generated</label>
                        <input type="number" name="leads_generated" class="form-control" value="<?= old('leads_generated', '0') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Qualified Leads</label>
                        <input type="number" name="qualified_leads" class="form-control" value="<?= old('qualified_leads', '0') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Converted Leads</label>
                        <input type="number" name="converted_leads" class="form-control" value="<?= old('converted_leads', '0') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Campaign Status</label>
                <select name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Planning">Planning</option>
                    <option value="Paused">Paused</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Campaign Creative & Strategy Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Target audience demographics, copy, creatives..."><?= old('description') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Performance Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/campaigns" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Launch Campaign</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
