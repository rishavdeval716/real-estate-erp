<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/branches">Branches</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Branch: <?= esc($branch['name']) ?></h1>
        <p class="page-subtitle">Update regional office details, contact person, and operational status</p>
    </div>
    <div>
        <a href="/branches" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Branches
        </a>
    </div>
</div>

<div class="card" style="max-width: 860px;">
    <div class="card-header">
        <h2 class="card-title">Branch Profile</h2>
    </div>
    <div class="card-body">
        <form action="/branches/update/<?= esc($branch['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="company_id" class="form-label required">Parent Company Organization</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= esc($comp['id']) ?>" <?= (old('company_id', $branch['company_id']) == $comp['id']) ? 'selected' : '' ?>>
                                    <?= esc($comp['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Branch Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $branch['name'])) ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="code" class="form-label required">Branch Code (Unique Identifier)</label>
                        <input type="text" name="code" id="code" class="form-control" value="<?= esc(old('code', $branch['code'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="manager_name" class="form-label">Branch Manager</label>
                        <input type="text" name="manager_name" id="manager_name" class="form-control" value="<?= esc(old('manager_name', $branch['manager_name'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= esc(old('phone', $branch['phone'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Branch Contact Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email', $branch['email'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Office Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"><?= esc(old('address', $branch['address'])) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="<?= esc(old('city', $branch['city'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="state" class="form-label">State</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?= esc(old('state', $branch['state'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" name="country" id="country" class="form-control" value="<?= esc(old('country', $branch['country'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="<?= esc(old('pincode', $branch['pincode'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" <?= old('status', $branch['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status', $branch['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/branches" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
