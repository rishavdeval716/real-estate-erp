<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/customers">Customers</a> &rsaquo;
            <span><?= isset($customer) ? 'Edit Customer' : 'Add Customer' ?></span>
        </div>
        <h1 class="page-title"><?= isset($customer) ? 'Edit Customer Profile' : 'Register New Customer' ?></h1>
        <p class="page-subtitle"><?= isset($customer) ? 'Update contact, address, or identification credentials.' : 'Create customer file for booking generation and transactions.' ?></p>
    </div>
    <div>
        <a href="/customers" class="btn btn-secondary">Back to Customers</a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-body">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= isset($customer) ? '/customers/update/' . $customer['id'] : '/customers/store' ?>">
            <?= csrf_field() ?>

            <?php if (!empty($selectedLead)): ?>
                <input type="hidden" name="lead_id" value="<?= esc($selectedLead['id']) ?>">
                <div class="alert alert-info" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong>Converting Lead:</strong> <?= esc($selectedLead['first_name'] . ' ' . $selectedLead['last_name']) ?> (<?= esc($selectedLead['lead_code']) ?>)
                        <div style="font-size: 0.8rem; color: var(--slate-600);">Phone: <?= esc($selectedLead['phone']) ?> &bull; Email: <?= esc($selectedLead['email']) ?></div>
                    </div>
                    <span class="badge badge-primary">Lead Conversion</span>
                </div>
            <?php endif; ?>

            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                Personal Details
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">First Name <span style="color: red;">*</span></label>
                    <input type="text" name="first_name" class="form-control" required value="<?= old('first_name', $customer['first_name'] ?? $selectedLead['first_name'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Last Name <span style="color: red;">*</span></label>
                    <input type="text" name="last_name" class="form-control" required value="<?= old('last_name', $customer['last_name'] ?? $selectedLead['last_name'] ?? '') ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Primary Phone <span style="color: red;">*</span></label>
                    <input type="text" name="phone" class="form-control" required value="<?= old('phone', $customer['phone'] ?? $selectedLead['phone'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Alternate Phone</label>
                    <input type="text" name="alternate_phone" class="form-control" value="<?= old('alternate_phone', $customer['alternate_phone'] ?? '') ?>">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= old('email', $customer['email'] ?? $selectedLead['email'] ?? '') ?>">
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 1rem;">
                Address Details
            </h3>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Residential Address</label>
                <textarea name="address" class="form-control" rows="2"><?= old('address', $customer['address'] ?? '') ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= old('city', $customer['city'] ?? $selectedLead['city'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?= old('state', $customer['state'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="<?= old('pincode', $customer['pincode'] ?? '') ?>">
                </div>
            </div>

            <h3 style="font-size: 1rem; font-weight: 600; color: var(--slate-800); border-bottom: 1px solid var(--slate-200); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 1rem;">
                KYC & Identification Proof
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">ID Proof Type</label>
                    <select name="id_proof_type" class="form-control">
                        <option value="">Select Document Type</option>
                        <option value="PAN Card" <?= old('id_proof_type', $customer['id_proof_type'] ?? '') === 'PAN Card' ? 'selected' : '' ?>>PAN Card</option>
                        <option value="Aadhaar Card" <?= old('id_proof_type', $customer['id_proof_type'] ?? '') === 'Aadhaar Card' ? 'selected' : '' ?>>Aadhaar Card</option>
                        <option value="Passport" <?= old('id_proof_type', $customer['id_proof_type'] ?? '') === 'Passport' ? 'selected' : '' ?>>Passport</option>
                        <option value="Voter ID" <?= old('id_proof_type', $customer['id_proof_type'] ?? '') === 'Voter ID' ? 'selected' : '' ?>>Voter ID</option>
                        <option value="Driving License" <?= old('id_proof_type', $customer['id_proof_type'] ?? '') === 'Driving License' ? 'selected' : '' ?>>Driving License</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">ID Proof Number</label>
                    <input type="text" name="id_proof_number" class="form-control" placeholder="e.g. ABCDE1234F" value="<?= old('id_proof_number', $customer['id_proof_number'] ?? '') ?>">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1rem;">
                <a href="/customers" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= isset($customer) ? 'Update Customer' : 'Save & Register Customer' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
