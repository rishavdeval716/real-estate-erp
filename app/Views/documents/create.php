<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/documents">Legal & Documents</a> &rsaquo;
            <span>Upload Document</span>
        </div>
        <h1 class="page-title">Upload Legal & Compliance Document</h1>
        <p class="page-subtitle">Attach title deeds, sanctioned architectural plans, NOCs, and statutory certificates.</p>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Compliance File Metadata</h3>
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

        <form method="POST" action="/documents/store" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Document Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Registered Conveyance Deed - Tower A" value="<?= old('title') ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Document Category <span style="color: var(--danger);">*</span></label>
                        <select name="document_category" class="form-control" required>
                            <option value="Ownership / Title Deed">Ownership / Title Deed</option>
                            <option value="Registry Document">Registry Document</option>
                            <option value="Building Approval Plan">Building Approval Plan</option>
                            <option value="NOC Clearance">NOC Clearance</option>
                            <option value="Occupancy Certificate">Occupancy Certificate</option>
                            <option value="RERA Certificate">RERA Certificate</option>
                            <option value="Encumbrance Certificate">Encumbrance Certificate</option>
                            <option value="Property Tax Receipt">Property Tax Receipt</option>
                            <option value="Legal Opinion">Legal Opinion</option>
                            <option value="KYC Document">KYC Document</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Property</label>
                        <select name="property_id" class="form-control">
                            <option value="">— Corporate General / Not Property Specific —</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?> (<?= esc($p['property_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Project</label>
                        <select name="project_id" class="form-control">
                            <option value="">— None —</option>
                            <?php foreach ($projects as $proj): ?>
                            <option value="<?= $proj['id'] ?>"><?= esc($proj['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Owner (Optional)</label>
                        <select name="owner_id" class="form-control">
                            <option value="">— None —</option>
                            <?php foreach ($owners as $o): ?>
                            <option value="<?= $o['id'] ?>"><?= esc($o['first_name'] . ' ' . $o['last_name']) ?> (<?= esc($o['owner_code']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="<?= old('issue_date') ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expiry Date (Leave blank if permanent)</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?= old('expiry_date') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Select Document File (.pdf, .png, .jpg, max 25MB)</label>
                <input type="file" name="document_file" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
            </div>

            <div class="form-group">
                <label class="form-label">Compliance Notes & Registration Number</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Sub-registrar entry number, volume/page, issuing municipal authority..."><?= old('notes') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/documents" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Upload Document</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
