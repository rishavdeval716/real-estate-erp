<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/verifications">Verifications</a> &rsaquo;
            <span><?= esc($verification['verification_code']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($verification['verification_type']) ?></h1>
        <p class="page-subtitle">Property: <?= esc($verification['property_title']) ?> (<?= esc($verification['property_code']) ?>) &bull; Audit: <?= esc($verification['verification_code']) ?></p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/verifications" class="btn btn-secondary">Back to Audits</a>
    </div>
</div>

<div class="form-row">
    <!-- Audit Findings -->
    <div class="form-col-7">
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0;">Audit Findings & Evidence</h3>
                <span class="badge <?= $verification['status'] === 'Verified' ? 'badge-success' : ($verification['status'] === 'Under Review' ? 'badge-warning' : 'badge-danger') ?>" style="font-size: 0.9rem; padding: 0.35rem 0.75rem;">
                    <?= esc($verification['status']) ?>
                </span>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <table class="table" style="font-size: 0.9rem; margin-bottom: 1.5rem;">
                    <tr>
                        <td style="width: 35%; color: var(--slate-500); font-weight: 600;">Audit Code</td>
                        <td><strong style="color: var(--primary);"><?= esc($verification['verification_code']) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Verification Type</td>
                        <td><span class="badge badge-info"><?= esc($verification['verification_type']) ?></span></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Property Title</td>
                        <td><strong><?= esc($verification['property_title']) ?></strong> (<?= esc($verification['property_code']) ?>)</td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Landlord / Owner</td>
                        <td><?= esc($verification['owner_name_or_reference'] ?: 'Direct Developer Asset') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Ownership Legal Details</td>
                        <td><?= esc($verification['ownership_details'] ?: 'Freehold clear commercial/residential title') ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--slate-500); font-weight: 600;">Auditor Findings</td>
                        <td><?= esc($verification['findings'] ?: 'Audit in progress...') ?></td>
                    </tr>
                    <?php if (!empty($verification['rejection_reason'])): ?>
                    <tr style="background: var(--danger-light);">
                        <td style="color: var(--danger); font-weight: 700;">Rejection Reason</td>
                        <td style="color: var(--danger); font-weight: 600;"><?= esc($verification['rejection_reason']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>

                <?php 
                $checklists = json_decode($verification['checklist_data'] ?? '{}', true);
                if (!empty($checklists)):
                ?>
                <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--slate-800); margin: 1.25rem 0 0.75rem;">Checklist Item Verification</h4>
                <div style="background: var(--slate-50); padding: 1rem; border-radius: 8px;">
                    <?php foreach ($checklists as $key => $passed): ?>
                    <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--slate-200);">
                        <span style="font-size: 0.85rem; color: var(--slate-700);"><?= ucwords(str_replace('_', ' ', $key)) ?></span>
                        <span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'Passed' : 'Defect' ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Conduct Audit Form -->
    <div class="form-col-5">
        <div class="card" style="border: 2px solid var(--primary-light);">
            <div class="card-header" style="background: var(--primary-light); border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--primary);">Issue Legal Audit Verdict</h3>
            </div>
            <div class="card-body" style="padding: 1.5rem;">
                <form method="POST" action="/verifications/conduct/<?= $verification['id'] ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Audit Verdict</label>
                        <select name="status" class="form-control" id="ver_status" onchange="toggleRejReason()">
                            <option value="Verified" <?= $verification['status'] === 'Verified' ? 'selected' : '' ?>>Verified Compliant</option>
                            <option value="Under Review" <?= $verification['status'] === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                            <option value="Rejected" <?= $verification['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected / Title Defect</option>
                            <option value="Pending" <?= $verification['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Expired" <?= $verification['status'] === 'Expired' ? 'selected' : '' ?>>Expired</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Detailed Auditor Observations</label>
                        <textarea name="findings" class="form-control" rows="4" placeholder="Detail verified deed records, municipal approval numbers, encumbrance search results..."><?= esc($verification['findings']) ?></textarea>
                    </div>

                    <div class="form-group" id="rej_box" style="display: <?= $verification['status'] === 'Rejected' ? 'block' : 'none' ?>;">
                        <label class="form-label">Rejection / Defect Details</label>
                        <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Explain grounds for rejection..."><?= esc($verification['rejection_reason']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Record Legal Verification Verdict</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRejReason() {
    var s = document.getElementById('ver_status').value;
    document.getElementById('rej_box').style.display = s === 'Rejected' ? 'block' : 'none';
}
</script>

<?= $this->endSection() ?>
