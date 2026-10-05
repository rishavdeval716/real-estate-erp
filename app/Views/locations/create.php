<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/locations">Locations</a>
            <span class="separator">/</span>
            <span>Create</span>
        </div>
        <h1 class="page-title">Add New Location</h1>
        <p class="page-subtitle">Register a geographic territory, locality, and landmark for property listings</p>
    </div>
    <div>
        <a href="/locations" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Locations
        </a>
    </div>
</div>

<div class="card" style="max-width: 860px;">
    <div class="card-header">
        <h2 class="card-title">Geographic Details</h2>
    </div>
    <div class="card-body">
        <form action="/locations/store" method="POST">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="state" class="form-label required">State / Province</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?= esc(old('state')) ?>" placeholder="e.g. Maharashtra, Karnataka, Delhi" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="city" class="form-label required">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="<?= esc(old('city')) ?>" placeholder="e.g. Mumbai, Pune, Bengaluru" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="area" class="form-label required">Area / Neighborhood</label>
                        <input type="text" name="area" id="area" class="form-control" value="<?= esc(old('area')) ?>" placeholder="e.g. Bandra West, Whitefield, Cyber City" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="pincode" class="form-label required">Pincode / Postal Code</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="<?= esc(old('pincode')) ?>" placeholder="e.g. 400050" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="locality" class="form-label">Locality / Sector</label>
                        <input type="text" name="locality" id="locality" class="form-control" value="<?= esc(old('locality')) ?>" placeholder="e.g. Pali Hill, Sector 42">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="landmark" class="form-label">Landmark</label>
                        <input type="text" name="landmark" id="landmark" class="form-control" value="<?= esc(old('landmark')) ?>" placeholder="e.g. Near Carter Road Promenade">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="nearby_locations" class="form-label">Nearby Locations / Key Hubs</label>
                <input type="text" name="nearby_locations" id="nearby_locations" class="form-control" value="<?= esc(old('nearby_locations')) ?>" placeholder="e.g. Khar West, Santacruz, Bandra-Worli Sea Link">
                <div class="form-hint">Comma separated list of prominent hubs or neighborhoods nearby.</div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="map_location" class="form-label">Map Coordinates / Link</label>
                        <input type="text" name="map_location" id="map_location" class="form-control" value="<?= esc(old('map_location')) ?>" placeholder="e.g. 19.0607° N, 72.8277° E or Google Maps URL">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="status" class="form-label required">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">Save Location</button>
                <a href="/locations" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
