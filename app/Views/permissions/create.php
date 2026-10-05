<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/permissions">Permissions</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Create New Permission</h1>
        <p class="page-subtitle">Register an authorization slug for module capability gates</p>
    </div>
    <div>
        <a href="/permissions" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Permissions
        </a>
    </div>
</div>

<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h2 class="card-title">Permission Specification</h2>
    </div>
    <div class="card-body">
        <form action="/permissions/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name" class="form-label required">Permission Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Export Property Financials" required>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="slug" class="form-label required">Slug / Unique Key</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="<?= esc(old('slug')) ?>" placeholder="e.g. properties.export" required>
                        <div class="form-hint">Format: <code>module.action</code> (lowercase, alphanumeric, dot-separated)</div>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="group_name" class="form-label required">Module Group</label>
                        <input type="text" name="group_name" id="group_name" list="group_options" class="form-control" value="<?= esc(old('group_name')) ?>" placeholder="e.g. Properties" required>
                        <datalist id="group_options">
                            <?php foreach ($defaultGroups as $dg): ?>
                                <option value="<?= esc($dg) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Explain what functionality this permission unlocks"><?= esc(old('description')) ?></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Create Permission</button>
                <a href="/permissions" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
