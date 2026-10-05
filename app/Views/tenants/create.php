<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/tenants">Tenants</a> &rsaquo;
            <span>Register Tenant</span>
        </div>
        <h1 class="page-title">Register New Tenant</h1>
        <p class="page-subtitle">Onboard resident or commercial leaseholder with contact and identity details.</p>
    </div>
</div>

<div class="card" style="max-width: 900px;">
    <div class="card-body">
        <form method="POST" action="/tenants/store">
            <?= csrf_field() ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">Tenant Type <span style="color: var(--rose-500);">*</span></label>
                    <select name="tenant_type" class="form-control" required id="tenantTypeSelect" onchange="toggleCompanyFields()">
                        <option value="individual" <?= old('tenant_type') === 'individual' ? 'selected' : '' ?>>Individual Tenant</option>
                        <option value="company" <?= old('tenant_type') === 'company' ? 'selected' : '' ?>>Company / Corporate Leaseholder</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name / Primary Leaseholder <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?= old('full_name') ?>" required placeholder="e.g. Vikramaditya Birla">
                </div>

                <div class="form-group company-field" id="companyNameGroup" style="display: none;">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="<?= old('company_name') ?>" placeholder="e.g. Nexus Tech Solutions Pvt Ltd">
                </div>

                <div class="form-group company-field" id="contactPersonGroup" style="display: none;">
                    <label class="form-label">Contact Person Name</label>
                    <input type="text" name="contact_person" class="form-control" value="<?= old('contact_person') ?>" placeholder="e.g. Rohan Deshmukh (Admin Head)">
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="<?= old('mobile') ?>" required placeholder="e.g. 9820011223">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address <span style="color: var(--rose-500);">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required placeholder="e.g. tenant@example.com">
                </div>

                <div class="form-group">
                    <label class="form-label">ID Proof Type</label>
                    <select name="id_proof_type" class="form-control">
                        <option value="">Select ID Type</option>
                        <option value="PAN Card" <?= old('id_proof_type') === 'PAN Card' ? 'selected' : '' ?>>PAN Card</option>
                        <option value="Aadhaar Card" <?= old('id_proof_type') === 'Aadhaar Card' ? 'selected' : '' ?>>Aadhaar Card</option>
                        <option value="Passport" <?= old('id_proof_type') === 'Passport' ? 'selected' : '' ?>>Passport</option>
                        <option value="Voter ID" <?= old('id_proof_type') === 'Voter ID' ? 'selected' : '' ?>>Voter ID</option>
                        <option value="Certificate of Incorporation" <?= old('id_proof_type') === 'Certificate of Incorporation' ? 'selected' : '' ?>>Certificate of Incorporation (Corporate)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ID / Registration Number</label>
                    <input type="text" name="id_proof_number" class="form-control" value="<?= old('id_proof_number') ?>" placeholder="e.g. ABCDE1234F">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Permanent / Registered Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full address line..."><?= old('address') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= old('city') ?>" placeholder="e.g. Mumbai">
                </div>

                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?= old('state') ?>" placeholder="e.g. Maharashtra">
                </div>

                <div class="form-group">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="<?= old('pincode') ?>" placeholder="e.g. 400050">
                </div>

                <div class="form-group">
                    <label class="form-label">Account Status</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Special Remarks / Internal Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any background checks or operational notes..."><?= old('notes') ?></textarea>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--slate-200);">
                <a href="/tenants" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Tenant Profile</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCompanyFields() {
    const val = document.getElementById('tenantTypeSelect').value;
    const compFields = document.querySelectorAll('.company-field');
    compFields.forEach(el => {
        el.style.display = (val === 'company') ? 'block' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', toggleCompanyFields);
</script>

<?= $this->endSection() ?>
