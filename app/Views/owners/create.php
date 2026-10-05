<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/owners">Property Owners</a> &rsaquo;
            <span>Register Owner</span>
        </div>
        <h1 class="page-title">Register Property Landlord / Owner</h1>
        <p class="page-subtitle">Onboard property owner, setup contact profiles, KYC records, and banking credentials.</p>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Landlord Profile Information</h3>
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

        <form method="POST" action="/owners/store">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" class="form-control" required value="<?= old('first_name') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Last Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="last_name" class="form-control" required value="<?= old('last_name') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Company / Trust Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Apex Holdings Ltd" value="<?= old('company_name') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color: var(--danger);">*</span></label>
                        <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Mobile Phone <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="+91 98..." value="<?= old('phone') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Alternate Phone</label>
                        <input type="text" name="alternate_phone" class="form-control" value="<?= old('alternate_phone') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Permanent Physical Address</label>
                <textarea name="address" class="form-control" rows="2"><?= old('address') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= old('city') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="<?= old('state') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="<?= old('pincode') ?>">
                    </div>
                </div>
            </div>

            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--slate-800); margin: 1.5rem 0 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1.25rem;">
                Statutory KYC & Banking Information
            </h4>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Income Tax PAN Number</label>
                        <input type="text" name="pan_number" class="form-control" placeholder="ABCDE1234F" style="text-transform: uppercase;" value="<?= old('pan_number') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Aadhaar / National ID</label>
                        <input type="text" name="aadhaar_number" class="form-control" placeholder="12-digit number" value="<?= old('aadhaar_number') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. HDFC Bank" value="<?= old('bank_name') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="<?= old('bank_account_number') ?>">
                    </div>
                </div>
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" name="bank_ifsc" class="form-control" style="text-transform: uppercase;" value="<?= old('bank_ifsc') ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">KYC Verification Status</label>
                        <select name="kyc_status" class="form-control">
                            <option value="pending">Pending Review</option>
                            <option value="verified">Verified Compliance</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Account Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Ownership Notes & Remarks</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/owners" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Owner Profile</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
