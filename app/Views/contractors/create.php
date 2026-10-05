<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <a href="/contractors" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
        &larr; Back to Contractors
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    <p style="color: var(--slate-500); font-size: 14px;">Register contractor credentials, contact person, trade specialization and tax registrations</p>
</div>

<div class="erp-card" style="max-width: 750px;">
    <div class="card-body" style="padding: 24px;">
        <form method="POST" action="/contractors/store">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Company / Enterprise Name *</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Apex Civil Works Ltd." required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Specialization *</label>
                        <select name="specialization" class="form-control" required>
                            <option value="Civil & Structural">Civil & Structural</option>
                            <option value="Electrical">Electrical & MEP</option>
                            <option value="Plumbing">Plumbing & Sanitary</option>
                            <option value="Finishing & Painting">Finishing & Painting</option>
                            <option value="HVAC">HVAC Ventilation</option>
                            <option value="Fire Safety">Fire Safety Systems</option>
                            <option value="Landscaping">Landscaping</option>
                            <option value="Other">Other Specialist Trade</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Contact Person *</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="Managing Director / Engineer" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Phone Number *</label>
                        <input type="text" name="phone" class="form-control" placeholder="+91 98200 00000" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Official Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="contracts@enterprise.local" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Initial Performance Rating (1-5)</label>
                        <input type="number" name="rating" class="form-control" value="5.0" step="0.1" min="1" max="5">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Trade License / Registration #</label>
                        <input type="text" name="license_number" class="form-control" placeholder="LIC-CIV-2026-XXXX">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">GSTIN Number</label>
                        <input type="text" name="gstin" class="form-control" placeholder="27AAAAA0000A1Z5">
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Status</label>
                    <select name="status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="/contractors" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Contractor Profile</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
