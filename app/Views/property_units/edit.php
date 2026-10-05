<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/units">Property Units</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
        <h1 class="page-title">Edit Unit: <?= esc($unit['unit_number']) ?></h1>
        <p class="page-subtitle">Update unit specifications, pricing, and availability</p>
    </div>
    <div>
        <a href="/units/view/<?= esc($unit['id']) ?>" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Unit
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px;">
    <div class="card-header">
        <h2 class="card-title">Edit Unit Details</h2>
    </div>
    <div class="card-body">
        <form action="/units/update/<?= esc($unit['id']) ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Project & Tower -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="project_id" class="form-label required">Parent Project</label>
                        <select name="project_id" id="project_id" class="form-control" required>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= old('project_id', $unit['project_id']) == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['name']) ?> (<?= esc($p['project_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="tower_id" class="form-label">Tower / Block</label>
                        <select name="tower_id" id="tower_id" class="form-control">
                            <option value="">Standalone / None</option>
                            <?php foreach ($towers as $twr): ?>
                                <option value="<?= esc($twr['id']) ?>" <?= old('tower_id', $unit['tower_id']) == $twr['id'] ? 'selected' : '' ?>>
                                    <?= esc($twr['tower_name']) ?> (<?= esc($twr['tower_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Property Linking & Unit Number -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="unit_number" class="form-label required">Unit Number / Code</label>
                        <input type="text" name="unit_number" id="unit_number" class="form-control" value="<?= esc(old('unit_number', $unit['unit_number'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="property_id" class="form-label">Associated Master Property</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">None (Independent Unit)</option>
                            <?php foreach ($properties as $prop): ?>
                                <option value="<?= esc($prop['id']) ?>" <?= old('property_id', $unit['property_id']) == $prop['id'] ? 'selected' : '' ?>>
                                    <?= esc($prop['title']) ?> (<?= esc($prop['property_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Floor, Flat Type & Facing -->
            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label for="floor" class="form-label required">Floor Number</label>
                        <input type="number" name="floor" id="floor" class="form-control" value="<?= esc(old('floor', $unit['floor'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="flat_type" class="form-label required">Flat Type / Class</label>
                        <input type="text" name="flat_type" id="flat_type" class="form-control" value="<?= esc(old('flat_type', $unit['flat_type'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="facing" class="form-label">Facing / Direction</label>
                        <input type="text" name="facing" id="facing" class="form-control" value="<?= esc(old('facing', $unit['facing'])) ?>">
                    </div>
                </div>
            </div>

            <!-- Areas & Balcony/Parking -->
            <div class="form-row">
                <div class="form-col-3">
                    <div class="form-group">
                        <label for="carpet_area" class="form-label required">Carpet Area (Sq.Ft)</label>
                        <input type="number" step="0.01" name="carpet_area" id="carpet_area" class="form-control" value="<?= esc(old('carpet_area', $unit['carpet_area'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="built_up_area" class="form-label required">Built-up Area (Sq.Ft)</label>
                        <input type="number" step="0.01" name="built_up_area" id="built_up_area" class="form-control" value="<?= esc(old('built_up_area', $unit['built_up_area'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="balcony" class="form-label">Balconies</label>
                        <input type="number" name="balcony" id="balcony" class="form-control" value="<?= esc(old('balcony', $unit['balcony'])) ?>" min="0">
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="parking" class="form-label">Parking Spaces</label>
                        <input type="number" name="parking" id="parking" class="form-control" value="<?= esc(old('parking', $unit['parking'])) ?>" min="0">
                    </div>
                </div>
            </div>

            <!-- Pricing & Status -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="unit_price" class="form-label required">Unit Price (₹)</label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="<?= esc(old('unit_price', $unit['unit_price'])) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="availability_status" class="form-label required">Availability Status</label>
                        <select name="availability_status" id="availability_status" class="form-control" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= esc($st) ?>" <?= old('availability_status', $unit['availability_status']) === $st ? 'selected' : '' ?>>
                                    <?= esc($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Update Unit</button>
                <a href="/units/view/<?= esc($unit['id']) ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
