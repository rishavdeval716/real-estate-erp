<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/users">Users</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add New User</h1>
        <p class="page-subtitle">Register a team member, configure their branch and access roles</p>
    </div>
    <div>
        <a href="/users" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Users
        </a>
    </div>
</div>

<div class="card" style="max-width: 860px;">
    <div class="card-header">
        <h2 class="card-title">User Account Details</h2>
    </div>
    <div class="card-body">
        <form action="/users/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" placeholder="e.g. Rahul Verma" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="email" class="form-label required">Corporate Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email')) ?>" placeholder="rahul.v@company.com" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= esc(old('phone')) ?>" placeholder="+91 98765 43210">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="branch_id" class="form-label">Assigned Branch</label>
                        <select name="branch_id" id="branch_id" class="form-control">
                            <option value="">-- Select Branch (Optional) --</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= esc($branch['id']) ?>" <?= old('branch_id') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?> (<?= esc($branch['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="status" class="form-label required">Account Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active (Can sign in)</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= old('status') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Assign System Roles</label>
                <div class="form-hint" style="margin-bottom: 0.75rem;">Check one or more roles defining user capabilities and permissions.</div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; background: var(--slate-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--slate-200);">
                    <?php 
                    $oldRoles = old('roles') ?? [];
                    foreach ($roles as $role): 
                    ?>
                        <label class="perm-checkbox-item">
                            <input type="checkbox" name="roles[]" value="<?= esc($role['id']) ?>" <?= in_array($role['id'], (array)$oldRoles) ? 'checked' : '' ?>>
                            <div>
                                <span class="perm-name"><?= esc($role['name']) ?></span>
                                <span class="perm-slug"><?= esc($role['description'] ?: 'Standard Role') ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create User Account</button>
                <a href="/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
