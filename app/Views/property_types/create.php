<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/property-types">Property Types</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add Property Type</h1>
        <p class="page-subtitle">Define a new property classification category</p>
    </div>
    <div>
        <a href="/property-types" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Types
        </a>
    </div>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <h2 class="card-title">Property Type Configuration</h2>
    </div>
    <div class="card-body">
        <form action="/property-types/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name" class="form-label required">Type Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Duplex Penthouse, Studio Apartment, Warehouse" required>
                <div class="form-hint">Unique name of the property classification.</div>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug / URL Key (Optional)</label>
                <input type="text" name="slug" id="slug" class="form-control" value="<?= esc(old('slug')) ?>" placeholder="Leave blank to auto-generate from name">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Provide details regarding the property class, usage, and zoning..."><?= esc(old('description')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Property Type</button>
                <a href="/property-types" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
