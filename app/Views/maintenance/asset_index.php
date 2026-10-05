<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/facility-assets">Operations</a> &rsaquo;
            <span>Facility Assets</span>
        </div>
        <h1 class="page-title">Facility Assets & Equipment Register</h1>
        <p class="page-subtitle">Track building infrastructure, elevators, diesel generators, HVAC plants, and warranty lifecycles.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openCreateAssetModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Register Facility Asset
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Assets</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Operational</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['operational']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Under Maintenance</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['under_maintenance']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Decommissioned</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);"><?= esc($kpi['decommissioned']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/facility-assets" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Asset name, code, location, property..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Category</label>
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="elevators" <?= $filters['category'] === 'elevators' ? 'selected' : '' ?>>Elevators</option>
                    <option value="generators" <?= $filters['category'] === 'generators' ? 'selected' : '' ?>>Generators</option>
                    <option value="fire_safety" <?= $filters['category'] === 'fire_safety' ? 'selected' : '' ?>>Fire Safety</option>
                    <option value="water_treatment" <?= $filters['category'] === 'water_treatment' ? 'selected' : '' ?>>Water Treatment</option>
                    <option value="electrical" <?= $filters['category'] === 'electrical' ? 'selected' : '' ?>>Electrical</option>
                    <option value="hvac" <?= $filters['category'] === 'hvac' ? 'selected' : '' ?>>HVAC</option>
                    <option value="other" <?= $filters['category'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="operational" <?= $filters['status'] === 'operational' ? 'selected' : '' ?>>Operational</option>
                    <option value="under_maintenance" <?= $filters['status'] === 'under_maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                    <option value="decommissioned" <?= $filters['status'] === 'decommissioned' ? 'selected' : '' ?>>Decommissioned</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/facility-assets" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Assets Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Property Location</th>
                        <th>Location Details</th>
                        <th>Installation Date</th>
                        <th>Warranty Expiry</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assets)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No facility assets found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($assets as $a): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($a['asset_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-800);"><?= esc($a['name']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="text-transform: capitalize; font-weight: 600;">
                                <?= str_replace('_', ' ', esc($a['category'])) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($a['property_title']) ?></div>
                        </td>
                        <td><?= esc($a['location_details'] ?? 'Building Plant Room') ?></td>
                        <td><?= !empty($a['installation_date']) ? date('d M Y', strtotime($a['installation_date'])) : '—' ?></td>
                        <td>
                            <?php if (!empty($a['warranty_expiry'])): ?>
                            <span style="font-weight: 500; color: <?= strtotime($a['warranty_expiry']) < time() ? 'var(--rose-600)' : 'var(--emerald-600)' ?>;">
                                <?= date('d M Y', strtotime($a['warranty_expiry'])) ?>
                            </span>
                            <?php else: ?>
                            <span style="color: var(--slate-400);">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $sBadge = match($a['status']) {
                                'operational'       => 'badge-success',
                                'under_maintenance' => 'badge-warning',
                                'decommissioned'    => 'badge-danger',
                                default             => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $sBadge ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($a['status'])) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openEditAssetModal(<?= htmlspecialchars(json_encode($a), ENT_QUOTES) ?>)">Edit</button>
                            <form method="POST" action="/facility-assets/delete/<?= $a['id'] ?>" onsubmit="return confirm('Decommission / remove facility asset <?= esc($a['asset_code']) ?>?');" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-secondary" style="color: var(--rose-600);">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
    <div class="card-footer" style="padding: 1rem;">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal: Register Asset -->
<div id="createAssetModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Register Facility Asset</h3>
            <button type="button" onclick="closeCreateAssetModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/facility-assets/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Asset Name / Model <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Otis Passenger Elevator 8-Passenger" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Category <span style="color: var(--rose-500);">*</span></label>
                        <select name="category" class="form-control" required>
                            <option value="elevators">Elevators & Lifts</option>
                            <option value="generators">Generators (DG Sets)</option>
                            <option value="fire_safety">Fire Safety & Hydrants</option>
                            <option value="water_treatment">Water Treatment & STP</option>
                            <option value="electrical">Electrical & Transformers</option>
                            <option value="hvac">HVAC & Chillers</option>
                            <option value="other">Other Equipment</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Property Location <span style="color: var(--rose-500);">*</span></label>
                        <select name="property_id" class="form-control" required>
                            <option value="">-- Choose Property --</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Location Details within Building</label>
                    <input type="text" name="location_details" class="form-control" placeholder="e.g. Basement 2 Utility Room, Tower B Roof">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Installation Date</label>
                        <input type="date" name="installation_date" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Warranty Expiry</label>
                        <input type="date" name="warranty_expiry" class="form-control">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Operating Status <span style="color: var(--rose-500);">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="operational">Operational</option>
                        <option value="under_maintenance">Under Maintenance</option>
                        <option value="decommissioned">Decommissioned</option>
                    </select>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeCreateAssetModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Asset -->
<div id="editAssetModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="editAssetModalTitle" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Edit Facility Asset</h3>
            <button type="button" onclick="closeEditAssetModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="editAssetForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Asset Name / Model <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="name" id="editAssetName" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Category <span style="color: var(--rose-500);">*</span></label>
                        <select name="category" id="editAssetCategory" class="form-control" required>
                            <option value="elevators">Elevators & Lifts</option>
                            <option value="generators">Generators (DG Sets)</option>
                            <option value="fire_safety">Fire Safety & Hydrants</option>
                            <option value="water_treatment">Water Treatment & STP</option>
                            <option value="electrical">Electrical & Transformers</option>
                            <option value="hvac">HVAC & Chillers</option>
                            <option value="other">Other Equipment</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Property Location <span style="color: var(--rose-500);">*</span></label>
                        <select name="property_id" id="editAssetProperty" class="form-control" required>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Location Details within Building</label>
                    <input type="text" name="location_details" id="editAssetLocation" class="form-control">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Installation Date</label>
                        <input type="date" name="installation_date" id="editAssetInstall" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Warranty Expiry</label>
                        <input type="date" name="warranty_expiry" id="editAssetWarranty" class="form-control">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Operating Status <span style="color: var(--rose-500);">*</span></label>
                    <select name="status" id="editAssetStatus" class="form-control" required>
                        <option value="operational">Operational</option>
                        <option value="under_maintenance">Under Maintenance</option>
                        <option value="decommissioned">Decommissioned</option>
                    </select>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeEditAssetModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Asset</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateAssetModal() {
    document.getElementById('createAssetModal').style.display = 'flex';
}
function closeCreateAssetModal() {
    document.getElementById('createAssetModal').style.display = 'none';
}

function openEditAssetModal(a) {
    document.getElementById('editAssetModalTitle').innerText = 'Edit ' + a.asset_code;
    document.getElementById('editAssetName').value = a.name;
    document.getElementById('editAssetCategory').value = a.category;
    document.getElementById('editAssetProperty').value = a.property_id;
    document.getElementById('editAssetLocation').value = a.location_details || '';
    document.getElementById('editAssetInstall').value = a.installation_date || '';
    document.getElementById('editAssetWarranty').value = a.warranty_expiry || '';
    document.getElementById('editAssetStatus').value = a.status;
    document.getElementById('editAssetForm').action = '/facility-assets/update/' + a.id;
    document.getElementById('editAssetModal').style.display = 'flex';
}
function closeEditAssetModal() {
    document.getElementById('editAssetModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
