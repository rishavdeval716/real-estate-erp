<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/campaigns">Marketing Campaigns</a> &rsaquo;
            <span>Edit Campaign</span>
        </div>
        <h1 class="page-title">Edit Campaign: <?= esc($campaign['campaign_code']) ?></h1>
        <p class="page-subtitle">Update campaign metrics, lead generation numbers, and allocated budget.</p>
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

        <form method="POST" action="/campaigns/update/<?= $campaign['id'] ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Campaign Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= old('name', $campaign['name']) ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign Channel Type <span style="color: var(--danger);">*</span></label>
                        <select name="campaign_type" class="form-control" required>
                            <?php foreach (['Social Media', 'Google Ads', 'Property Portal', 'Hoarding / Outdoor', 'Print Media', 'Email Marketing', 'SMS Campaign', 'Event / Expo', 'Other'] as $ct): ?>
                            <option value="<?= $ct ?>" <?= $campaign['campaign_type'] === $ct ? 'selected' : '' ?>><?= $ct ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Target Project (Optional)</label>
                        <select name="project_id" class="form-control">
                            <option value="">— Select Target Project —</option>
                            <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>" <?= $campaign['project_id'] == $proj['id'] ? 'selected' : '' ?>><?= esc($proj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign Start Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="start_date" class="form-control" required value="<?= old('start_date', $campaign['start_date']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Campaign End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= old('end_date', $campaign['end_date']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Allocated Budget (₹) <span style="color: var(--danger);">*</span></label>
                        <input type="number" step="0.01" name="budget" class="form-control" required value="<?= old('budget', $campaign['budget']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Actual Spend to Date (₹)</label>
                        <input type="number" step="0.01" name="actual_spend" class="form-control" value="<?= old('actual_spend', $campaign['actual_spend']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Leads Generated</label>
                        <input type="number" name="leads_generated" class="form-control" value="<?= old('leads_generated', $campaign['leads_generated']) ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Qualified Leads</label>
                        <input type="number" name="qualified_leads" class="form-control" value="<?= old('qualified_leads', $campaign['qualified_leads']) ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Converted Leads</label>
                        <input type="number" name="converted_leads" class="form-control" value="<?= old('converted_leads', $campaign['converted_leads']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Campaign Status</label>
                <select name="status" class="form-control">
                    <option value="Active" <?= $campaign['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Planning" <?= $campaign['status'] === 'Planning' ? 'selected' : '' ?>>Planning</option>
                    <option value="Paused" <?= $campaign['status'] === 'Paused' ? 'selected' : '' ?>>Paused</option>
                    <option value="Completed" <?= $campaign['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $campaign['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Campaign Creative & Strategy Description</label>
                <textarea name="description" class="form-control" rows="2"><?= old('description', $campaign['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Performance Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes', $campaign['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/campaigns/view/<?= $campaign['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Campaign</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
