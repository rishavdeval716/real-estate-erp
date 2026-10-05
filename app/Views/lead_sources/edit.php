<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/lead-sources">Lead Sources</a> &rsaquo;
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Lead Source: <?= esc($source['name']) ?></h1>
        <p class="page-subtitle">Update acquisition source attributes and availability status.</p>
    </div>
    <div>
        <a href="/lead-sources" class="btn btn-secondary">&larr; Back to Sources</a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <form method="post" action="/lead-sources/update/<?= esc($source['id']) ?>">
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="name">Source Name <span style="color: var(--danger);">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required value="<?= old('name', $source['name']) ?>">
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="slug">Slug Identifier <span style="color: var(--danger);">*</span></label>
            <input type="text" name="slug" id="slug" class="form-control" required value="<?= old('slug', $source['slug']) ?>">
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"><?= old('description', $source['description']) ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="active" <?= old('status', $source['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= old('status', $source['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
            <a href="/lead-sources" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Lead Source</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
