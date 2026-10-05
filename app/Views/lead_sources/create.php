<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/lead-sources">Lead Sources</a> &rsaquo;
            <span>Create</span>
        </div>
        <h1 class="page-title">Add Lead Source</h1>
        <p class="page-subtitle">Configure a new marketing channel for lead attribution.</p>
    </div>
    <div>
        <a href="/lead-sources" class="btn btn-secondary">&larr; Back to Sources</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <form method="post" action="/lead-sources/store">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="name">Source Name <span style="color: var(--danger);">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="e.g. LinkedIn Ads, Walk-in, Billboard..." value="<?= old('name') ?>">
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="slug">Slug Identifier <span style="color: var(--slate-500); font-weight: 400;">(leave blank to auto-generate)</span></label>
            <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. linkedin-ads" value="<?= old('slug') ?>">
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Campaign, UTM tag reference, or channel details..."><?= old('description') ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="/lead-sources" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Lead Source</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
