<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <a href="/procurement" style="font-size: 13px; font-weight: 600; color: var(--primary); display: inline-flex; align-items: center; gap: 4px; margin-bottom: 8px;">
        &larr; Back to Requisitions
    </a>
    <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900);"><?= esc($title) ?></h1>
    <p style="color: var(--slate-500); font-size: 14px;">Raise material indent for construction site supplies, structural steel, cement and masonry</p>
</div>

<div class="erp-card" style="max-width: 750px;">
    <div class="card-body" style="padding: 24px;">
        <form method="POST" action="/procurement/store">
            <?= csrf_field() ?>
            <div style="display: flex; flex-direction: column; gap: 18px;">
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
                            <option value="">All Towers / Site-wide</option>
                            <?php foreach ($towers as $tw): ?>
                                <option value="<?= $tw['id'] ?>"><?= esc($tw['tower_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Material Item Name *</label>
                        <input type="text" name="item_name" class="form-control" placeholder="e.g. Ultratech 53 Grade OPC Cement (50kg)" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Category *</label>
                        <select name="category" class="form-control" required>
                            <option value="Cement">Cement</option>
                            <option value="Steel">Steel & Rebar</option>
                            <option value="Masonry">Masonry & Blocks</option>
                            <option value="Aggregates">Sand & Aggregates</option>
                            <option value="Electrical">Electrical Conduits & Wire</option>
                            <option value="Plumbing">Plumbing Pipes & Valves</option>
                            <option value="Finishing">Tiles, Paint & Glazing</option>
                            <option value="Safety">Safety & Hardware</option>
                            <option value="Other">Other Supplies</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Quantity *</label>
                        <input type="number" id="reqQty" name="quantity" class="form-control" placeholder="100" step="0.01" min="0.01" required oninput="calcTotal()">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Unit of Measure *</label>
                        <select name="unit_of_measure" class="form-control" required>
                            <option value="Bags">Bags (50kg)</option>
                            <option value="Metric Tons">Metric Tons (MT)</option>
                            <option value="Pieces">Pieces / Units</option>
                            <option value="Meters">Meters</option>
                            <option value="Sq.Ft">Square Feet</option>
                            <option value="Truckloads">Truckloads / Brass</option>
                            <option value="Liters">Liters</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Est. Unit Rate (₹) *</label>
                        <input type="number" id="reqRate" name="estimated_unit_cost" class="form-control" placeholder="380" step="0.01" min="0" required oninput="calcTotal()">
                    </div>
                </div>

                <div style="background: var(--slate-50); padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--slate-600);">Estimated Total Cost:</span>
                    <span id="reqTotalPreview" style="font-size: 18px; font-weight: 700; color: var(--primary);">₹0.00</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Required on Site By *</label>
                        <input type="date" name="required_by_date" class="form-control" required>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Priority Level</label>
                        <select name="priority" class="form-control">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                            <option value="Urgent">Urgent / Critical Pour</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 600; color: var(--slate-700); margin-bottom: 6px; display: block;">Remarks / Technical Grade</label>
                    <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Batch test certificates required upon delivery..."></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="/procurement" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Material Indent</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcTotal() {
    const q = parseFloat(document.getElementById('reqQty').value) || 0;
    const r = parseFloat(document.getElementById('reqRate').value) || 0;
    const total = q * r;
    document.getElementById('reqTotalPreview').innerText = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
<?= $this->endSection() ?>
