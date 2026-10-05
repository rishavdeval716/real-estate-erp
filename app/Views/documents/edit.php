<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/documents">Legal & Documents</a> &rsaquo;
            <span>Edit Document</span>
        </div>
        <h1 class="page-title">Edit Document: <?= esc($document['document_code']) ?></h1>
        <p class="page-subtitle">Update document title, category, and expiration schedule.</p>
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

        <form method="POST" action="/documents/update/<?= $document['id'] ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Document Title <span style="color: var(--danger);">*</span></label>
                <input type="text" name="title" class="form-control" required value="<?= old('title', $document['title']) ?>">
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Document Category <span style="color: var(--danger);">*</span></label>
                        <select name="document_category" class="form-control" required>
                            <?php foreach (['Ownership / Title Deed', 'Registry Document', 'Building Approval Plan', 'NOC Clearance', 'Occupancy Certificate', 'RERA Certificate', 'Encumbrance Certificate', 'Property Tax Receipt', 'Legal Opinion', 'KYC Document', 'Other'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= $document['document_category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Associated Property</label>
                        <select name="property_id" class="form-control">
                            <option value="">— Corporate General / Not Property Specific —</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $document['property_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
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
                            <option value="<?= $proj['id'] ?>" <?= $document['project_id'] == $proj['id'] ? 'selected' : '' ?>><?= esc($proj['name']) ?></option>
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
                            <option value="<?= $o['id'] ?>" <?= $document['owner_id'] == $o['id'] ? 'selected' : '' ?>><?= esc($o['first_name'] . ' ' . $o['last_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="<?= old('issue_date', $document['issue_date']) ?>">
                    </div>
                </div>
                <div class="form-col-6">
                    <div class="form-group">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="<?= old('expiry_date', $document['expiry_date']) ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Compliance Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= old('notes', $document['notes']) ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="/documents/view/<?= $document['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Document</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
