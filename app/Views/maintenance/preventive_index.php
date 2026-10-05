<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>Preventive Maintenance</span>
        </div>
        <h1 class="page-title">Preventive Maintenance & AMC Schedules</h1>
        <p class="page-subtitle">Schedule recurring building asset inspections, manage annual maintenance contracts (AMC), and auto-advance service dates.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openScheduleModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Schedule Preventive Service
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Schedules</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['total']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Scheduled / Active</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-600);"><?= esc($kpi['scheduled']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Completed Cycles</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);"><?= esc($kpi['completed']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Overdue Inspections</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);"><?= esc($kpi['overdue']) ?></div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/preventive-maintenance" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Facility Asset</label>
                <select name="asset_id" class="form-control">
                    <option value="">All Facility Assets</option>
                    <?php foreach ($assets as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= $filters['asset_id'] == $a['id'] ? 'selected' : '' ?>>
                        <?= esc($a['name']) ?> (<?= esc($a['asset_code']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="scheduled" <?= $filters['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="completed" <?= $filters['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="overdue" <?= $filters['status'] === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                <a href="/preventive-maintenance" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Schedules Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Schedule Code</th>
                        <th>Facility Equipment</th>
                        <th>Premises</th>
                        <th>Maintenance Type</th>
                        <th>Frequency</th>
                        <th>Next Due Date</th>
                        <th>Assigned Specialist</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--slate-500);">
                            No preventive maintenance schedules found.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($schedules as $s): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($s['schedule_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($s['asset_name']) ?></div>
                            <span class="badge badge-light" style="font-size: 0.7rem;"><?= esc($s['asset_code']) ?></span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; font-weight: 500;"><?= esc($s['property_title']) ?></div>
                        </td>
                        <td><?= esc($s['maintenance_type']) ?></td>
                        <td>
                            <span class="badge badge-light" style="text-transform: capitalize; font-weight: 600;">
                                <?= str_replace('_', ' ', esc($s['frequency'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $isOverdue = strtotime($s['next_service_date']) < time() && $s['status'] !== 'completed';
                            ?>
                            <div style="font-weight: 600; color: <?= $isOverdue ? 'var(--rose-600)' : 'var(--slate-800)' ?>;">
                                <?= date('d M Y', strtotime($s['next_service_date'])) ?>
                            </div>
                            <?php if (!empty($s['last_service_date'])): ?>
                            <div style="font-size: 0.75rem; color: var(--slate-400);">Last: <?= date('d M Y', strtotime($s['last_service_date'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= esc($s['technician_name'] ?? 'Unassigned Specialist') ?>
                        </td>
                        <td>
                            <?php
                            $sBadge = match($s['status']) {
                                'completed' => 'badge-success',
                                'overdue'   => 'badge-danger',
                                default     => 'badge-info',
                            };
                            ?>
                            <span class="badge <?= $sBadge ?>" style="text-transform: capitalize;">
                                <?= esc($s['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <form method="POST" action="/preventive-maintenance/complete/<?= $s['id'] ?>" onsubmit="return confirm('Mark this maintenance cycle as completed? Next service date will automatically advance based on <?= esc($s['frequency']) ?> frequency.');" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-secondary" title="Complete & Advance Next Date">
                                    &check; Complete Service
                                </button>
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

<!-- Modal: Schedule Preventive Service -->
<div id="scheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Schedule Preventive AMC Service</h3>
            <button type="button" onclick="closeScheduleModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/preventive-maintenance/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Facility Asset / Equipment <span style="color: var(--rose-500);">*</span></label>
                    <select name="asset_id" class="form-control" required>
                        <option value="">-- Choose Asset --</option>
                        <?php foreach ($assets as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= esc($a['name']) ?> (<?= esc($a['asset_code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Maintenance Job Title <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="maintenance_type" class="form-control" placeholder="e.g. Quarterly Chiller Servicing, DG Set Load Test" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Service Frequency <span style="color: var(--rose-500);">*</span></label>
                        <select name="frequency" class="form-control" required>
                            <option value="daily">Daily Inspection</option>
                            <option value="weekly">Weekly Routine</option>
                            <option value="monthly" selected>Monthly Service</option>
                            <option value="quarterly">Quarterly Comprehensive</option>
                            <option value="semi_annual">Semi-Annual Audit</option>
                            <option value="annual">Annual Overhaul (AMC)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Next Service Date <span style="color: var(--rose-500);">*</span></label>
                        <input type="date" name="next_service_date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 month')) ?>" required>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Assigned Service Specialist</label>
                    <select name="assigned_technician_id" class="form-control">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($technicians as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?> (<?= esc($t['skill']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Instructions / Checklist Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Oil pressure check, filter replacement checklist..."></textarea>
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeScheduleModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
function openScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'flex';
}
function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
