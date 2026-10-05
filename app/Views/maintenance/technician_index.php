<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/technicians">Operations</a> &rsaquo;
            <span>Technicians</span>
        </div>
        <h1 class="page-title">Service Technicians & Engineering Roster</h1>
        <p class="page-subtitle">Manage internal technicians, certified trade skills, current availability, and active work dispatches.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openCreateTechModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Service Technician
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Technicians</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Available for Dispatch</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['available']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Busy on Active Jobs</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--amber-600);"><?= esc($kpi['busy']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">On Leave / Roster Off</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-400);"><?= esc($kpi['on_leave']) ?></div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/technicians" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Technician name, code, skill, department..." value="<?= esc($filters['search']) ?>">
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Availability</label>
                <select name="availability" class="form-control">
                    <option value="">All Availabilities</option>
                    <option value="available" <?= $filters['availability'] === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="busy" <?= $filters['availability'] === 'busy' ? 'selected' : '' ?>>Busy</option>
                    <option value="on_leave" <?= $filters['availability'] === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/technicians" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Technicians Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tech Code</th>
                        <th>Technician Name</th>
                        <th>Trade Skill</th>
                        <th>Department</th>
                        <th>Contact Mobile</th>
                        <th>Email Address</th>
                        <th>Availability</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($technicians)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No service technicians registered.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($technicians as $t): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($t['technician_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--slate-800);"><?= esc($t['name']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="font-weight: 600;"><?= esc($t['skill']) ?></span>
                        </td>
                        <td><?= esc($t['department']) ?></td>
                        <td>
                            <a href="tel:<?= esc($t['mobile']) ?>" style="color: var(--slate-700); font-weight: 500;"><?= esc($t['mobile']) ?></a>
                        </td>
                        <td>
                            <a href="mailto:<?= esc($t['email']) ?>" style="color: var(--slate-500);"><?= esc($t['email']) ?></a>
                        </td>
                        <td>
                            <?php
                            $avBadge = match($t['availability']) {
                                'available' => 'badge-success',
                                'busy'      => 'badge-warning',
                                'on_leave'  => 'badge-secondary',
                                default     => 'badge-light',
                            };
                            ?>
                            <span class="badge <?= $avBadge ?>" style="text-transform: capitalize;">
                                <?= str_replace('_', ' ', esc($t['availability'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $t['status'] === 'active' ? 'badge-success' : 'badge-danger' ?>" style="text-transform: capitalize;">
                                <?= esc($t['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="openEditTechModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)">Edit</button>
                            <form method="POST" action="/technicians/delete/<?= $t['id'] ?>" onsubmit="return confirm('Remove technician <?= esc($t['name']) ?>?');" style="display: inline;">
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

<!-- Modal: Add Technician -->
<div id="createTechModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Add Service Technician</h3>
            <button type="button" onclick="closeCreateTechModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/technicians/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Technician Full Name <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Ramesh Chandra" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Contact Mobile <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Email Address <span style="color: var(--rose-500);">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="tech@realestate-erp.local" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Trade Skill / License <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="skill" class="form-control" placeholder="e.g. Master Electrician, HVAC Chiller" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Department <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Power & Electrical, MEP" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Availability <span style="color: var(--rose-500);">*</span></label>
                        <select name="availability" class="form-control" required>
                            <option value="available" selected>Available for Dispatch</option>
                            <option value="busy">Busy on Active Jobs</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Roster Status <span style="color: var(--rose-500);">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeCreateTechModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Technician</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Technician -->
<div id="editTechModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="editTechModalTitle" style="font-size: 1.1rem; font-weight: 700; margin: 0;">Edit Technician</h3>
            <button type="button" onclick="closeEditTechModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form id="editTechForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Technician Full Name <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="name" id="editTechName" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Contact Mobile <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="mobile" id="editTechMobile" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Email Address <span style="color: var(--rose-500);">*</span></label>
                        <input type="email" name="email" id="editTechEmail" class="form-control" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Trade Skill / License <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="skill" id="editTechSkill" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Department <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="department" id="editTechDept" class="form-control" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Availability <span style="color: var(--rose-500);">*</span></label>
                        <select name="availability" id="editTechAvail" class="form-control" required>
                            <option value="available">Available for Dispatch</option>
                            <option value="busy">Busy on Active Jobs</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Roster Status <span style="color: var(--rose-500);">*</span></label>
                        <select name="status" id="editTechStatus" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeEditTechModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Technician</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateTechModal() {
    document.getElementById('createTechModal').style.display = 'flex';
}
function closeCreateTechModal() {
    document.getElementById('createTechModal').style.display = 'none';
}

function openEditTechModal(t) {
    document.getElementById('editTechModalTitle').innerText = 'Edit ' + t.technician_code;
    document.getElementById('editTechName').value = t.name;
    document.getElementById('editTechMobile').value = t.mobile;
    document.getElementById('editTechEmail').value = t.email;
    document.getElementById('editTechSkill').value = t.skill;
    document.getElementById('editTechDept').value = t.department;
    document.getElementById('editTechAvail').value = t.availability;
    document.getElementById('editTechStatus').value = t.status;
    document.getElementById('editTechForm').action = '/technicians/update/' + t.id;
    document.getElementById('editTechModal').style.display = 'flex';
}
function closeEditTechModal() {
    document.getElementById('editTechModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
