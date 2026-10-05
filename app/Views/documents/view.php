<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/documents">Legal & Documents</a> &rsaquo;
            <span><?= esc($document['document_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($document['title']) ?></h1>
        <p class="page-subtitle"><?= esc($document['document_category']) ?> &bull; Code: <?= esc($document['document_code']) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/documents/edit/<?= $document['id'] ?>" class="btn btn-secondary">Edit Document</a>
        <a href="/documents" class="btn btn-secondary">Back to Documents</a>
    </div>
</div>

<div class="form-row">
    <!-- Document Overview -->
    <div class="form-col-7">
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Compliance File Profile</h3>
                <span class="badge <?= $document['verification_status'] === 'Verified' ? 'badge-success' : ($document['verification_status'] === 'Pending' ? 'badge-warning' : 'badge-danger') ?>" style="font-size: 0.9rem; padding: 0.35rem 0.75rem;">
                    <?= esc($document['verification_status']) ?>
                </span>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <table class="table" style="font-size: 0.9rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Document Code</td>
                        <td><strong style="color: var(--primary);"><?= esc($document['document_code']) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Category</td>
                        <td><span class="badge badge-info"><?= esc($document['document_category']) ?></span></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Associated Property</td>
                        <td><?= esc($document['property_title'] ?: 'Corporate Master') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Associated Project</td>
                        <td><?= esc($document['project_name'] ?: 'None') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Associated Owner</td>
                        <td><?= $document['owner_fname'] ? esc($document['owner_fname'] . ' ' . $document['owner_lname']) : 'None' ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Issue Date</td>
                        <td><?= $document['issue_date'] ? date('d F Y', strtotime($document['issue_date'])) : '—' ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Expiry Date</td>
                        <td>
                            <?php if ($document['expiry_date']): ?>
                            <?= date('d F Y', strtotime($document['expiry_date'])) ?>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">Permanent / Perpetual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Stored File Name</td>
                        <td><code><?= esc($document['file_name']) ?></code> (<?= number_format($document['file_size'] / 1024, 0) ?> KB)</td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Compliance Notes</td>
                        <td><?= esc($document['notes'] ?: 'No notes provided.') ?></td>
                    </tr>
                    <?php if (!empty($document['rejection_reason'])): ?>
                    <tr style="background: var(--danger-light);">
                        <td style="color: var(--danger); font-weight: 700;">Rejection Reason</td>
                        <td style="color: var(--danger); font-weight: 600;"><?= esc($document['rejection_reason']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Legal Verification Panel -->
    <div class="form-col-5">
        <div class="card" style="border: 2px solid var(--primary-light);">
            <div class="card-header" style="background: var(--primary-light); border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--primary);">Document Verification Panel</h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <form method="POST" action="/documents/verify/<?= $document['id'] ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Update Verification Status</label>
                        <select name="verification_status" class="form-control" id="ver_status" onchange="toggleRejection()">
                            <option value="Verified" <?= $document['verification_status'] === 'Verified' ? 'selected' : '' ?>>Verified & Compliant</option>
                            <option value="Under Review" <?= $document['verification_status'] === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="Pending" <?= $document['verification_status'] === 'Pending' ? 'selected' : '' ?>>Pending Review</option>
                            <option value="Rejected" <?= $document['verification_status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            <option value="Expired" <?= $document['verification_status'] === 'Expired' ? 'selected' : '' ?>>Expired</option>
                        </select>
                    </div>

                    <div class="form-group" id="rejection_box" style="display: <?= $document['verification_status'] === 'Rejected' ? 'block' : 'none' ?>;">
                        <label class="form-label">Rejection Reason / Defects</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Explain missing seal, blurriness, or legal discrepancy..."><?= esc($document['rejection_reason']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Record Legal Verification Verdict</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRejection() {
    var status = document.getElementById('ver_status').value;
    document.getElementById('rejection_box').style.display = status === 'Rejected' ? 'block' : 'none';
}
</script>

<?= $this->endSection() ?>
