<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <span>Properties</span>
        </div>
        <h1 class="page-title">Property Listings & Inventory</h1>
        <p class="page-subtitle">Master property catalogue with multi-criteria search, pricing, and availability management</p>
    </div>
    <div>
        <?php if (session()->get('is_super_admin') || in_array('properties.create', session()->get('permissions') ?? [])): ?>
            <a href="/properties/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Property
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Advanced Multi-Criteria Property Search & Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="padding: 0.75rem 1.25rem;">
        <h3 class="card-title" style="font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Advanced Property Filter & Search
        </h3>
    </div>
    <div class="card-body" style="padding: 1.25rem;">
        <form method="GET" action="/properties">
            <div class="form-row">
                <div class="form-col-4">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Keyword Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title, property code, owner, locality..." value="<?= esc($search) ?>">
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Property Type</label>
                        <select name="type_id" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t['id']) ?>" <?= $typeId == $t['id'] ? 'selected' : '' ?>>
                                    <?= esc($t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Project</label>
                        <select name="project_id" class="form-control form-control-sm">
                            <option value="">All Projects</option>
                            <?php foreach ($projects as $p): ?>
                                <option value="<?= esc($p['id']) ?>" <?= $projectId == $p['id'] ? 'selected' : '' ?>>
                                    <?= esc($p['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-3">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Location / City</label>
                        <select name="location_id" class="form-control form-control-sm">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= esc($loc['id']) ?>" <?= $locationId == $loc['id'] ? 'selected' : '' ?>>
                                    <?= esc($loc['city']) ?> - <?= esc($loc['area']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Min Price (₹)</label>
                        <input type="number" name="min_price" class="form-control form-control-sm" placeholder="e.g. 5000000" value="<?= esc($minPrice ?? '') ?>">
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Max Price (₹)</label>
                        <input type="number" name="max_price" class="form-control form-control-sm" placeholder="e.g. 50000000" value="<?= esc($maxPrice ?? '') ?>">
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Min Area (Sq.Ft)</label>
                        <input type="number" name="min_area" class="form-control form-control-sm" placeholder="e.g. 1000" value="<?= esc($minArea ?? '') ?>">
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Max Area (Sq.Ft)</label>
                        <input type="number" name="max_area" class="form-control form-control-sm" placeholder="e.g. 5000" value="<?= esc($maxArea ?? '') ?>">
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Availability Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= esc($st) ?>" <?= $status === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-col-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.8rem;">Sort By</label>
                        <select name="sort_by" class="form-control form-control-sm">
                            <option value="id" <?= $sortBy === 'id' ? 'selected' : '' ?>>Recently Added</option>
                            <option value="price" <?= $sortBy === 'price' ? 'selected' : '' ?>>Price</option>
                            <option value="area" <?= $sortBy === 'area' ? 'selected' : '' ?>>Area</option>
                            <option value="title" <?= $sortBy === 'title' ? 'selected' : '' ?>>Title</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.25rem;">
                <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                <a href="/properties" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Property Listing Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Property Code</th>
                        <th>Property Title</th>
                        <th>Type</th>
                        <th>Project</th>
                        <th>Location</th>
                        <th>Area (sq.ft)</th>
                        <th>Price (₹)</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($properties)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                No properties found matching your filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($properties as $prop): ?>
                            <tr>
                                <td><code><?= esc($prop['property_code']) ?></code></td>
                                <td>
                                    <div style="font-weight: 700; color: var(--slate-900);">
                                        <a href="/properties/view/<?= esc($prop['id']) ?>" style="color: inherit; text-decoration: none;">
                                            <?= esc($prop['title']) ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($prop['owner_name_or_reference'])): ?>
                                        <div style="font-size: 0.76rem; color: var(--slate-500);">Owner: <?= esc($prop['owner_name_or_reference']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--slate-100); color: var(--slate-800); font-weight: 600;">
                                        <?= esc($prop['property_type_name'] ?? '—') ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.88rem; color: var(--slate-700);">
                                    <?= esc($prop['project_name'] ?: 'Standalone') ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-800);"><?= esc($prop['location_area'] ?? '') ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($prop['location_city'] ?? '') ?></div>
                                </td>
                                <td style="font-weight: 600;"><?= number_format((float)$prop['area'], 2) ?></td>
                                <td style="font-weight: 700; color: var(--primary);">₹<?= number_format((float)$prop['price'], 2) ?></td>
                                <td>
                                    <?= \App\Libraries\PropertyStatus::renderBadge($prop['status']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 0.4rem;">
                                        <a href="/properties/view/<?= esc($prop['id']) ?>" class="btn btn-sm btn-secondary" title="View Property Details">
                                            View
                                        </a>
                                        <?php if (session()->get('is_super_admin') || in_array('properties.edit', session()->get('permissions') ?? [])): ?>
                                            <a href="/properties/edit/<?= esc($prop['id']) ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if (session()->get('is_super_admin') || in_array('properties.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/properties/delete/<?= esc($prop['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this property?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="card-footer" style="display: flex; justify-content: flex-end; padding: 1rem;">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
