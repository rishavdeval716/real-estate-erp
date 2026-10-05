<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/company">Company</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Corporate Profile</h1>
        <p class="page-subtitle">Update enterprise branding, legal tax identifiers, and contact details</p>
    </div>
    <div>
        <a href="/company" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Profile
        </a>
    </div>
</div>

<div class="card" style="max-width: 920px;">
    <div class="card-header">
        <h2 class="card-title">Corporate Master Details</h2>
    </div>
    <div class="card-body">
        <form action="/company/update" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="name" class="form-label required">Company Legal Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $company['name'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="tax_number" class="form-label">Tax / GST Number</label>
                        <input type="text" name="tax_number" id="tax_number" class="form-control" value="<?= esc(old('tax_number', $company['tax_number'])) ?>" placeholder="e.g. 27AAAAA0000A1Z5">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Corporate Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email', $company['email'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="website" class="form-label">Official Website</label>
                        <input type="url" name="website" id="website" class="form-control" value="<?= esc(old('website', $company['website'])) ?>" placeholder="https://company.com">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="phone" class="form-label">Primary Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= esc(old('phone', $company['phone'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="alternate_phone" class="form-label">Alternate Phone</label>
                        <input type="text" name="alternate_phone" id="alternate_phone" class="form-control" value="<?= esc(old('alternate_phone', $company['alternate_phone'])) ?>">
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="form-group">
                <label for="logo" class="form-label">Company Logo (PNG, JPG, WEBP - Max 2MB)</label>
                <?php if (!empty($company['logo']) && file_exists(FCPATH . ltrim($company['logo'], '/'))): ?>
                    <div style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 1rem;">
                        <img src="<?= esc($company['logo']) ?>" alt="Current Logo" style="max-height: 50px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); padding: 0.25rem;">
                        <span style="font-size: 0.8rem; color: var(--slate-500);">Current logo registered. Upload a new image to replace it.</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="logo" id="logo" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
            </div>

            <!-- Address Section -->
            <div class="form-group">
                <label for="address" class="form-label">Physical Address</label>
                <textarea name="address" id="address" class="form-control" rows="2"><?= esc(old('address', $company['address'])) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="<?= esc(old('city', $company['city'])) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="state" class="form-label">State / Province</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?= esc(old('state', $company['state'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" name="country" id="country" class="form-control" value="<?= esc(old('country', $company['country'] ?: 'India')) ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="pincode" class="form-label">Postal / Zip Code</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="<?= esc(old('pincode', $company['pincode'])) ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" <?= old('status', $company['status']) === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status', $company['status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Organization Description / Scope</label>
                <textarea name="description" id="description" class="form-control" rows="3"><?= esc(old('description', $company['description'])) ?></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Profile</button>
                <a href="/company" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
