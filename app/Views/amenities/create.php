<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/amenities">Amenities</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add Property Amenity</h1>
        <p class="page-subtitle">Configure an amenity option for property specifications</p>
    </div>
    <div>
        <a href="/amenities" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Amenities
        </a>
    </div>
</div>

<div class="card" style="max-width: 650px;">
    <div class="card-header">
        <h2 class="card-title">Amenity Configuration</h2>
    </div>
    <div class="card-body">
        <form action="/amenities/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name" class="form-label required">Amenity Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Swimming Pool, Gymnasium, Power Backup" required>
            </div>

            <div class="form-group">
                <label for="icon" class="form-label">Icon Tag / Identifier</label>
                <input type="text" name="icon" id="icon" class="form-control" value="<?= esc(old('icon', 'ri-checkbox-circle-line')) ?>" placeholder="e.g. ri-heart-pulse-line">
                <div class="form-hint">Visual icon tag identifier.</div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Brief explanation of facility features or maintenance..."><?= esc(old('description')) ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Amenity</button>
                <a href="/amenities" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
