<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/units">Property Units</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add Property Unit</h1>
        <p class="page-subtitle">Configure an individual flat, penthouse, shop, or commercial suite</p>
    </div>
    <div>
        <a href="/units" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Units
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px;">
    <div class="card-header">
        <h2 class="card-title">Unit Specifications</h2>
    </div>
    <div class="card-body">
        <form action="/units/store" method="POST">
            <?= csrf_field() ?>

            <!-- Project & Tower -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="project_id" class="form-label required">Parent Project</label>
                        <select name="project_id" id="project_id" class="form-control" onchange="window.location.href='/units/create?project_id='+this.value" required>
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= ($projectId == $p['id'] || old('project_id') == $p['id']) ? 'selected' : '' ?>>
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
                                <option value="<?= esc($twr['id']) ?>" <?= old('tower_id') == $twr['id'] ? 'selected' : '' ?>>
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
                        <input type="text" name="unit_number" id="unit_number" class="form-control" value="<?= esc(old('unit_number')) ?>" placeholder="e.g. A-1201, W1-702, Villa 04" required>
                        <div class="form-hint">Must be unique within the selected project and tower.</div>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="property_id" class="form-label">Associated Master Property (Optional)</label>
                        <select name="property_id" id="property_id" class="form-control">
                            <option value="">None (Independent Unit)</option>
                            <?php foreach ($properties as $prop): ?>
                                <option value="<?= esc($prop['id']) ?>" <?= old('property_id') == $prop['id'] ? 'selected' : '' ?>>
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
                        <input type="number" name="floor" id="floor" class="form-control" value="<?= esc(old('floor', '1')) ?>" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="flat_type" class="form-label required">Flat Type / Unit Class</label>
                        <input type="text" name="flat_type" id="flat_type" class="form-control" value="<?= esc(old('flat_type')) ?>" placeholder="e.g. 2 BHK, 3 BHK, Penthouse, Office Suite" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="facing" class="form-label">Facing / Direction</label>
                        <input type="text" name="facing" id="facing" class="form-control" value="<?= esc(old('facing')) ?>" placeholder="e.g. East, North, Sea View">
                    </div>
                </div>
            </div>

            <!-- Areas & Balcony/Parking -->
            <div class="form-row">
                <div class="form-col-3">
                    <div class="form-group">
                        <label for="carpet_area" class="form-label required">Carpet Area (Sq.Ft)</label>
                        <input type="number" step="0.01" name="carpet_area" id="carpet_area" class="form-control" value="<?= esc(old('carpet_area')) ?>" placeholder="e.g. 1150" required>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="built_up_area" class="form-label required">Built-up Area (Sq.Ft)</label>
                        <input type="number" step="0.01" name="built_up_area" id="built_up_area" class="form-control" value="<?= esc(old('built_up_area')) ?>" placeholder="e.g. 1450" required>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="balcony" class="form-label">Balconies</label>
                        <input type="number" name="balcony" id="balcony" class="form-control" value="<?= esc(old('balcony', '1')) ?>" min="0">
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label for="parking" class="form-label">Parking Spaces</label>
                        <input type="number" name="parking" id="parking" class="form-control" value="<?= esc(old('parking', '1')) ?>" min="0">
                    </div>
                </div>
            </div>

            <!-- Pricing & Status -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="unit_price" class="form-label required">Unit Price (₹)</label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control" value="<?= esc(old('unit_price')) ?>" placeholder="e.g. 12500000" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="availability_status" class="form-label required">Availability Status</label>
                        <select name="availability_status" id="availability_status" class="form-control" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= esc($st) ?>" <?= old('availability_status', 'Available') === $st ? 'selected' : '' ?>>
                                    <?= esc($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Property Unit</button>
                <a href="/units" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
