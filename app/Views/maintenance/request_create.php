<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/maintenance">Operations</a> &rsaquo;
            <span>Create Work Order</span>
        </div>
        <h1 class="page-title">Create Maintenance Ticket</h1>
        <p class="page-subtitle">Log building repair requests, link facility equipment, and calculate automatic SLA targets.</p>
    </div>
    <div>
        <a href="/maintenance" class="btn btn-secondary">
            &larr; Back to Work Orders
        </a>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-body" style="padding: 2rem;">
        <form method="POST" action="/maintenance/store">
            <?= csrf_field() ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Target Property <span style="color: var(--rose-500);">*</span></label>
                    <select name="property_id" class="form-control" required>
                        <option value="">-- Choose Property --</option>
                        <?php foreach ($properties as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= old('property_id') == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Unit (Optional)</label>
                    <select name="property_unit_id" class="form-control">
                        <option value="">-- Common Area / Entire Building --</option>
                        <?php foreach ($units as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= old('property_unit_id') == $u['id'] ? 'selected' : '' ?>>
                            Unit <?= esc($u['unit_number']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Tenant Occupant (Optional)</label>
                    <select name="tenant_id" class="form-control">
                        <option value="">-- Facility Admin / Building Log --</option>
                        <?php foreach ($tenants as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= old('tenant_id') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['full_name']) ?> (<?= esc($t['tenant_code']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Linked Facility Asset (Optional)</label>
                    <select name="asset_id" class="form-control">
                        <option value="">-- Not Linked to Specific Asset --</option>
                        <?php foreach ($assets as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= old('asset_id') == $a['id'] ? 'selected' : '' ?>>
                            <?= esc($a['name']) ?> (<?= esc($a['asset_code']) ?>) &bull; <?= ucfirst(esc($a['category'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Service Category <span style="color: var(--rose-500);">*</span></label>
                    <select name="category" class="form-control" required>
                        <option value="electrical">Electrical & Lighting</option>
                        <option value="plumbing">Plumbing & Water Supply</option>
                        <option value="hvac">HVAC & Air Conditioning</option>
                        <option value="elevators">Elevators & Lifts</option>
                        <option value="fire_safety">Fire Safety & Alarms</option>
                        <option value="carpentry">Carpentry & Structural</option>
                        <option value="other">General Facility Maintenance</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Subcategory / Component</label>
                    <input type="text" name="subcategory" class="form-control" placeholder="e.g. Master Circuit Breaker, Chiller Pump" value="<?= old('subcategory') ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label" style="font-weight: 500;">Priority & SLA Level <span style="color: var(--rose-500);">*</span></label>
                    <select name="priority" class="form-control" required>
                        <option value="urgent">Urgent (4hr resolution target)</option>
                        <option value="high">High (8hr resolution target)</option>
                        <option value="medium" selected>Medium (24hr resolution target)</option>
                        <option value="low">Low (48hr resolution target)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-weight: 500;">Assign Technician (Optional)</label>
                    <select name="assigned_technician_id" class="form-control">
                        <option value="">-- Unassigned (Hold in Open Pool) --</option>
                        <?php foreach ($technicians as $tech): ?>
                        <option value="<?= $tech['id'] ?>" <?= old('assigned_technician_id') == $tech['id'] ? 'selected' : '' ?>>
                            <?= esc($tech['name']) ?> (<?= esc($tech['skill']) ?>) &bull; <?= ucfirst(esc($tech['availability'])) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label class="form-label" style="font-weight: 500;">Detailed Defect / Work Description <span style="color: var(--rose-500);">*</span></label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe the symptom, location, equipment noises, leakages, or electrical faults..." required><?= old('description') ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--slate-200);">
                <a href="/maintenance" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    Dispatch Work Order
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
