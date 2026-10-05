<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/roles">Roles</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Create New Role</h1>
        <p class="page-subtitle">Define role credentials and configure assigned capability permissions</p>
    </div>
    <div>
        <a href="/roles" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Roles
        </a>
    </div>
</div>

<form action="/roles/store" method="POST">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Role Configuration</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Role Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Regional Supervisor" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-col-12">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="description" class="form-label">Role Description</label>
                        <input type="text" name="description" id="description" class="form-control" value="<?= esc(old('description')) ?>" placeholder="Detailed scope of responsibility">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--slate-900); margin-bottom: 0.35rem;">Permission Capabilities Matrix</h3>
        <p style="font-size: 0.86rem; color: var(--slate-500); margin-bottom: 1.25rem;">Select functional permissions granted to accounts with this role.</p>

        <div class="perm-matrix-grid">
            <?php 
            $oldPerms = old('permissions') ?? [];
            foreach ($groupedPermissions as $groupName => $perms): 
            ?>
                <div class="perm-group-card">
                    <div class="perm-group-header">
                        <span><?= esc($groupName) ?> (<?= count($perms) ?>)</span>
                        <button type="button" class="btn btn-sm btn-secondary select-all-group" style="padding: 0.2rem 0.5rem; font-size: 0.72rem;">Select All</button>
                    </div>
                    <div class="perm-items-list">
                        <?php foreach ($perms as $p): ?>
                            <label class="perm-checkbox-item">
                                <input type="checkbox" name="permissions[]" value="<?= esc($p['id']) ?>" <?= in_array($p['id'], (array)$oldPerms) ? 'checked' : '' ?>>
                                <div>
                                    <span class="perm-name"><?= esc($p['name']) ?></span>
                                    <span class="perm-slug"><?= esc($p['slug']) ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display: flex; gap: 0.75rem; margin-bottom: 3rem;">
        <button type="submit" class="btn btn-primary">Save New Role</button>
        <a href="/roles" class="btn btn-secondary">Cancel</a>
    </div>
</form>
<?= $this->endSection() ?>
