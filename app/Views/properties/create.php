<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/properties">Properties</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add New Property</h1>
        <p class="page-subtitle">Register a complete property profile, specifications, pricing, and amenities</p>
    </div>
    <div>
        <a href="/properties" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Properties
        </a>
    </div>
</div>

<div class="card" style="max-width: 960px;">
    <div class="card-header">
        <h2 class="card-title">Property Information</h2>
    </div>
    <div class="card-body">
        <form action="/properties/store" method="POST">
            <?= csrf_field() ?>

            <!-- Title & Code -->
            <div class="form-row">
                <div class="form-col-8">
                    <div class="form-group">
                        <label for="title" class="form-label required">Property Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= esc(old('title')) ?>" placeholder="e.g. 3 BHK Sea-View Luxury Apartment in Bandra" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="property_code" class="form-label required">Property Code (Unique)</label>
                        <input type="text" name="property_code" id="property_code" class="form-control" value="<?= esc(old('property_code')) ?>" placeholder="e.g. PROP-MUM-009" required>
                    </div>
                </div>
            </div>

            <!-- Type, Project & Location -->
            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label for="property_type_id" class="form-label required">Property Type</label>
                        <select name="property_type_id" id="property_type_id" class="form-control" required>
                            <option value="">Select Property Type</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t['id']) ?>" <?= old('property_type_id') == $t['id'] ? 'selected' : '' ?>>
                                    <?= esc($t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="location_id" class="form-label required">Location</label>
                        <select name="location_id" id="location_id" class="form-control" required>
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= esc($loc['id']) ?>" <?= old('location_id') == $loc['id'] ? 'selected' : '' ?>>
                                    <?= esc($loc['city']) ?> - <?= esc($loc['area']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="project_id" class="form-label">Project (Optional)</label>
                        <select name="project_id" id="project_id" class="form-control">
                            <option value="">Standalone / None</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= old('project_id') == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description" class="form-label">Property Description</label>
                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Detailed architectural description, view, floor level, interiors..."><?= esc(old('description')) ?></textarea>
            </div>

            <!-- Area, Price & Status -->
            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label for="area" class="form-label required">Area (Sq.Ft)</label>
                        <input type="number" step="0.01" name="area" id="area" class="form-control" value="<?= esc(old('area')) ?>" placeholder="e.g. 1850" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="price" class="form-label required">Price (₹)</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= esc(old('price')) ?>" placeholder="e.g. 45000000" required>
                    </div>
                </div>

                <div class="form-col-4">
                    <div class="form-group">
                        <label for="status" class="form-label required">Availability Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= esc($st) ?>" <?= old('status', 'Available') === $st ? 'selected' : '' ?>>
                                    <?= esc($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Ownership Details -->
            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="owner_name_or_reference" class="form-label">Owner Name or Reference</label>
                        <input type="text" name="owner_name_or_reference" id="owner_name_or_reference" class="form-control" value="<?= esc(old('owner_name_or_reference')) ?>" placeholder="e.g. Primary Developer or Private Owner Name">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="ownership_details" class="form-label">Ownership Details / Title Deed</label>
                        <input type="text" name="ownership_details" id="ownership_details" class="form-control" value="<?= esc(old('ownership_details')) ?>" placeholder="e.g. Freehold Clear Title, RERA Registered">
                    </div>
                </div>
            </div>

            <!-- Amenities Checkboxes -->
            <div class="form-group" style="margin-top: 1rem;">
                <label class="form-label">Select Amenities Available</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.6rem; background: var(--slate-50); padding: 1rem; border-radius: 6px; border: 1px solid var(--slate-200);">
                    <?php foreach ($amenities as $am): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="amenities[]" value="<?= esc($am['id']) ?>" <?= in_array($am['id'], old('amenities', [])) ? 'checked' : '' ?>>
                            <span><?= esc($am['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Property</button>
                <a href="/properties" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
