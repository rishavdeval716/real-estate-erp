<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/verifications">Verifications</a> &rsaquo;
            <span>Schedule Audit</span>
        </div>
        <h1 class="page-title">Schedule Compliance Audit</h1>
        <p class="page-subtitle">Initiate legal title search, municipal approval check, or physical site inspection.</p>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Audit Parameters & Checkpoints</h3>
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

        <form method="POST" action="/verifications/store">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Target Property <span style="color: var(--danger);">*</span></label>
                        <select name="property_id" class="form-control" required>
                            <option value="">— Select Target Property —</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?> (<?= esc($p['property_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Audit / Verification Type <span style="color: var(--danger);">*</span></label>
                        <select name="verification_type" class="form-control" required>
                            <option value="Legal Title Clearance">Legal Title Clearance</option>
                            <option value="Physical Property Audit">Physical Property Audit</option>
                            <option value="RERA Compliance Check">RERA Compliance Check</option>
                            <option value="Municipal Approval Check">Municipal Approval Check</option>
                            <option value="Structural & Fire Safety">Structural & Fire Safety</option>
                        </select>
                    </div>
                </div>
            </div>

            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--slate-800); margin: 1.5rem 0 0.75rem; border-top: 1px solid var(--slate-200); padding-top: 1.25rem;">
                Verification Checklist Items
            </h4>

            <div style="background: var(--slate-50); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="check_title" value="1" checked>
                    <span>30-Year Unbroken Title Search & Conveyance Verified</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="check_encumbrance" value="1" checked>
                    <span>Non-Encumbrance Certificate (Form 15/16) Cleared</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="check_tax" value="1" checked>
                    <span>Municipal Property Tax & Assessment Dues Cleared</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="check_litigation" value="1" checked>
                    <span>Civil Court & High Court Pending Litigation Search Clear</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                    <input type="checkbox" name="check_rera" value="1" checked>
                    <span>RERA Project Registration Active & Compliant</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Initial Findings / Auditor Notes</label>
                <textarea name="findings" class="form-control" rows="3" placeholder="Enter findings, sub-registrar search remarks, legal observations..."><?= old('findings') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Audit Verdict / Status</label>
                        <select name="status" class="form-control">
                            <option value="Pending">Pending</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Verified">Verified Compliant</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Certificate Expiry Date (Optional)</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?= old('expiry_date') ?>">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/verifications" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Schedule Audit</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
