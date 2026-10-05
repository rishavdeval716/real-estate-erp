<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <a href="/inspections" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
        &larr; Back to Inspections
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    <p style="color: var(--slate-500); font-size: 14px;">Log structural audits, MEP safety checks, and pre-possession snagging inspections</p>
</div>

<div class="erp-card" style="max-width: 750px;">
    <div class="card-body" style="padding: 24px;">
        <form method="POST" action="/inspections/store">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Inspection Type *</label>
                        <select name="inspection_type" class="form-control" required>
                            <option value="Structural Integrity">Structural Integrity & Rebar Inspection</option>
                            <option value="Pre-Pour Slab Checklist">Pre-Pour Slab Inspection</option>
                            <option value="Electrical Safety">Electrical Safety & Megger Testing</option>
                            <option value="Plumbing & Waterproofing">Plumbing Pressure & Waterproofing Ponding Test</option>
                            <option value="Pre-Plaster Quality">Pre-Plaster & Masonry Alignment Check</option>
                            <option value="Fire Safety & NOC">Fire Safety & Wet Riser Hydrant Check</option>
                            <option value="Pre-Possession Snagging Checklist">Pre-Possession Snagging Checklist (Buyer Unit)</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Inspection Date *</label>
                        <input type="date" name="inspection_date" class="form-control" value="<?= esc($today) ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Project *</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $prj): ?>
                                <option value="<?= $prj['id'] ?>"><?= esc($prj['name'] ?? $prj['project_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Building Tower (Optional)</label>
                        <select name="tower_id" class="form-control">
                            <option value="">All Towers</option>
                            <?php foreach ($towers as $tw): ?>
                                <option value="<?= $tw['id'] ?>"><?= esc($tw['tower_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Specific Property Unit (If Snagging Check)</label>
                    <select name="unit_id" class="form-control">
                        <option value="">N/A - General Common Areas / Structural</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?= $u['id'] ?>">Unit <?= esc($u['unit_number']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Inspection Result *</label>
                        <select name="result" class="form-control" required>
                            <option value="Passed">Passed - Zero Critical Defects</option>
                            <option value="Conditional Pass">Conditional Pass - Minor Snags</option>
                            <option value="Failed">Failed - Immediate Rectification Required</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Snags / Defects Found</label>
                        <input type="number" name="snags_found" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Defect Details & Snagging List</label>
                    <textarea name="snag_details" class="form-control" rows="3" placeholder="List itemized snags, e.g. door lock loose, paint patch on north wall, tile hollow sound..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Rectification Deadline</label>
                        <input type="date" name="rectification_deadline" class="form-control">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Auditor / Consultant Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="e.g. Complies with IS 456 / NBC 2016 guidelines">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="/inspections" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Record Inspection</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
