<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Property Header -->
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/properties">Properties</a>
            <span class="separator">/</span>
            <span><?= esc($property['property_code']) ?></span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
            <h1 class="page-title" style="margin: 0;"><?= esc($property['title']) ?></h1>
            <?= \App\Libraries\PropertyStatus::renderBadge($property['status']) ?>
        </div>
        <p class="page-subtitle" style="margin-top: 0.25rem;">
            <code><?= esc($property['property_code']) ?></code> &bull;
            <?= esc($property['property_type_name']) ?> &bull;
            <?= esc($property['area']) ?>, <?= esc($property['city']) ?> (<?= esc($property['state']) ?>)
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        <?php if (session()->get('is_super_admin') || in_array('availability.edit', session()->get('permissions') ?? [])): ?>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalChangeStatus').style.display='block'">
                Change Status
            </button>
        <?php endif; ?>
        <?php if (session()->get('is_super_admin') || in_array('pricing.create', session()->get('permissions') ?? [])): ?>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAddPricing').style.display='block'">
                + Add Valuation
            </button>
        <?php endif; ?>
        <?php if (session()->get('is_super_admin') || in_array('properties.edit', session()->get('permissions') ?? [])): ?>
            <a href="/properties/edit/<?= esc($property['id']) ?>" class="btn btn-primary">Edit Property</a>
        <?php endif; ?>
    </div>
</div>

<!-- Highlight Metrics Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.5rem;">
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Listing Price</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format((float)$property['price'], 2) ?></div>
            <div class="metric-meta">
                <span>₹<?= (float)$property['area'] > 0 ? number_format((float)$property['price'] / (float)$property['area'], 2) : 0 ?> / sq.ft</span>
            </div>
        </div>
    </div>
    <div class="metric-card info">
        <div>
            <div class="metric-label">Super Built-up Area</div>
            <div class="metric-value"><?= number_format((float)$property['area'], 2) ?></div>
            <div class="metric-meta"><span>Square Feet</span></div>
        </div>
    </div>
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Property Type</div>
            <div class="metric-value" style="font-size: 1.25rem;"><?= esc($property['property_type_name']) ?></div>
            <div class="metric-meta"><span><?= esc($property['project_name'] ? 'In ' . $property['project_name'] : 'Standalone') ?></span></div>
        </div>
    </div>
    <div class="metric-card success">
        <div>
            <div class="metric-label">Current Availability</div>
            <div class="metric-value" style="font-size: 1.25rem;"><?= esc($property['status']) ?></div>
            <div class="metric-meta"><span><?= count($units) ?> registered unit(s)</span></div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--slate-200); margin-bottom: 1.5rem;" id="propTabs">
    <button type="button" class="tab-btn active" onclick="switchTab('overview', this)">Overview</button>
    <button type="button" class="tab-btn" onclick="switchTab('amenities', this)">Amenities (<?= count($amenities) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab('units', this)">Units (<?= count($units) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab('media', this)">Media & Documents (<?= count($media) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab('pricing', this)">Pricing History (<?= count($pricingHistory) ?>)</button>
    <button type="button" class="tab-btn" onclick="switchTab('statusHistory', this)">Status History (<?= count($statusHistory) ?>)</button>
</div>

<!-- Tab 1: Overview -->
<div id="tab-overview" class="tab-content" style="display: block;">
    <div class="form-row">
        <div class="form-col-7">
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h2 class="card-title">Property Description</h2>
                </div>
                <div class="card-body">
                    <div style="font-size: 0.95rem; line-height: 1.6; color: var(--slate-700);">
                        <?= nl2br(esc($property['description'] ?: 'No detailed description provided.')) ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Ownership & Legal Details</h2>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Owner / Listing Reference</div>
                        <div style="font-weight: 600; color: var(--slate-900); font-size: 1rem;"><?= esc($property['owner_name_or_reference'] ?: 'Direct Enterprise Inventory') ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Ownership Details & Legal Title</div>
                        <div style="font-size: 0.9rem; color: var(--slate-700); margin-top: 0.25rem;">
                            <?= nl2br(esc($property['ownership_details'] ?: 'Freehold clear property title.')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-col-5">
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h2 class="card-title">Location & Project Information</h2>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Project</div>
                        <div style="font-weight: 600; color: var(--slate-900);">
                            <?php if ($property['project_id']): ?>
                                <a href="/projects/view/<?= esc($property['project_id']) ?>" style="color: var(--primary); text-decoration: none;">
                                    <?= esc($property['project_name']) ?> (<?= esc($property['project_code_ref']) ?>)
                                </a>
                                <div style="font-size: 0.8rem; color: var(--slate-500);">Developer: <?= esc($property['builder_developer']) ?></div>
                            <?php else: ?>
                                Standalone Listing (No Master Project)
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Location Details</div>
                        <div style="font-weight: 600; color: var(--slate-900);"><?= esc($property['area']) ?>, <?= esc($property['city']) ?></div>
                        <div style="font-size: 0.82rem; color: var(--slate-500);"><?= esc($property['locality']) ?> <?= $property['landmark'] ? ' &bull; Near ' . esc($property['landmark']) : '' ?></div>
                        <div style="font-size: 0.82rem; color: var(--slate-500);"><?= esc($property['state']) ?> - <?= esc($property['pincode']) ?></div>
                    </div>

                    <?php if (!empty($property['nearby_locations'])): ?>
                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Nearby Infrastructure</div>
                            <div style="font-size: 0.85rem; color: var(--slate-700);"><?= esc($property['nearby_locations']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($property['map_location'])): ?>
                        <div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--slate-400); font-weight: 700;">Coordinates / Map</div>
                            <code style="font-size: 0.82rem;"><?= esc($property['map_location']) ?></code>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab 2: Amenities -->
<div id="tab-amenities" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Associated Amenities & Features</h2>
        </div>
        <div class="card-body">
            <?php if (empty($amenities)): ?>
                <div style="text-align: center; color: var(--slate-400); padding: 3rem;">
                    No amenities assigned to this property yet. Click "Edit Property" to assign amenities.
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
                    <?php foreach ($amenities as $am): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem; background: var(--slate-50); padding: 0.85rem 1rem; border-radius: 6px; border: 1px solid var(--slate-200);">
                            <div style="width: 32px; height: 32px; border-radius: 6px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                ✓
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--slate-900); font-size: 0.92rem;"><?= esc($am['name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($am['description'] ?: 'Active feature') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tab 3: Units -->
<div id="tab-units" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title">Property Units</h2>
                <div class="card-subtitle">Flats, units, or suites mapped to this property</div>
            </div>
            <?php if ($property['project_id']): ?>
                <a href="/units/create?project_id=<?= esc($property['project_id']) ?>&property_id=<?= esc($property['id']) ?>" class="btn btn-sm btn-primary">
                    + Add Unit
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Unit #</th>
                            <th>Tower</th>
                            <th>Floor</th>
                            <th>Type</th>
                            <th>Carpet Area</th>
                            <th>Built-up Area</th>
                            <th>Price</th>
                            <th>Availability</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($units)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                    No units mapped to this property listing yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($units as $u): ?>
                                <tr>
                                    <td>
                                        <a href="/units/view/<?= esc($u['id']) ?>" style="font-weight: 700; color: var(--primary); text-decoration: none;">
                                            <?= esc($u['unit_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($u['tower_name'] ?: 'Standalone') ?></td>
                                    <td>Floor <?= esc($u['floor']) ?></td>
                                    <td><?= esc($u['flat_type']) ?></td>
                                    <td><?= number_format((float)$u['carpet_area'], 2) ?> sq.ft</td>
                                    <td><?= number_format((float)$u['built_up_area'], 2) ?> sq.ft</td>
                                    <td style="font-weight: 700;">₹<?= number_format((float)$u['unit_price'], 2) ?></td>
                                    <td><?= \App\Libraries\PropertyStatus::renderBadge($u['availability_status']) ?></td>
                                    <td style="text-align: right;">
                                        <a href="/units/view/<?= esc($u['id']) ?>" class="btn btn-sm btn-secondary">View Unit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tab 4: Media & Documents -->
<div id="tab-media" class="tab-content" style="display: none;">
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title">Media & Documents Gallery</h2>
                <div class="card-subtitle">Photos, floor plans, brochures, videos, and virtual tours</div>
            </div>
            <?php if (session()->get('is_super_admin') || in_array('media.create', session()->get('permissions') ?? [])): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modalUploadMedia').style.display='block'">
                    + Upload Media
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($media)): ?>
                <div style="text-align: center; color: var(--slate-400); padding: 3rem;">
                    No media or documents uploaded yet. Click "+ Upload Media" to add photos or floor plans.
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem;">
                    <?php foreach ($media as $m): ?>
                        <div class="card" style="margin: 0; overflow: hidden; border: 1px solid var(--slate-200);">
                            <div style="height: 140px; background: var(--slate-100); display: flex; align-items: center; justify-content: center; position: relative;">
                                <?php if (in_array(strtolower(pathinfo($m['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                    <img src="/media/file/<?= esc($m['id']) ?>" alt="<?= esc($m['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="text-align: center; color: var(--slate-500);">
                                        <div style="font-size: 2rem; font-weight: 700; text-transform: uppercase;"><?= strtoupper(pathinfo($m['file_name'], PATHINFO_EXTENSION)) ?></div>
                                        <div style="font-size: 0.75rem;"><?= esc($m['media_type']) ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($m['is_primary']): ?>
                                    <span style="position: absolute; top: 8px; left: 8px; background: var(--primary); color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                        PRIMARY
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div style="padding: 0.85rem;">
                                <div style="font-weight: 600; color: var(--slate-900); font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= esc($m['title']) ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 0.2rem;">
                                    <?= ucfirst(esc($m['media_type'])) ?> &bull; <?= round($m['file_size'] / 1024, 1) ?> KB
                                </div>

                                <div style="display: flex; gap: 0.4rem; margin-top: 0.75rem;">
                                    <a href="/media/file/<?= esc($m['id']) ?>" target="_blank" class="btn btn-sm btn-secondary" style="flex: 1; text-align: center;">
                                        View
                                    </a>
                                    <?php if (!$m['is_primary'] && in_array(strtolower(pathinfo($m['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                        <form action="/media/primary/<?= esc($m['id']) ?>" method="POST" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Set as primary photo">★</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (session()->get('is_super_admin') || in_array('media.delete', session()->get('permissions') ?? [])): ?>
                                        <form action="/media/delete/<?= esc($m['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete media file?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">✕</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tab 5: Pricing History -->
<div id="tab-pricing" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title">Pricing & Valuation Schedules</h2>
                <div class="card-subtitle">Historical pricing records, revisions, discounts, and market valuation</div>
            </div>
            <?php if (session()->get('is_super_admin') || in_array('pricing.create', session()->get('permissions') ?? [])): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modalAddPricing').style.display='block'">
                    + Add New Valuation
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Base Price</th>
                            <th>Price / Sq.Ft</th>
                            <th>Market Valuation</th>
                            <th>Negotiated Price</th>
                            <th>Discount</th>
                            <th>Effective Period</th>
                            <th>Remarks</th>
                            <th>Logged On</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pricingHistory)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                    No pricing history recorded for this property.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pricingHistory as $pr): ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--primary);">₹<?= number_format((float)$pr['base_price'], 2) ?></td>
                                    <td>₹<?= number_format((float)($pr['price_per_sqft'] ?? 0), 2) ?></td>
                                    <td><?= $pr['market_price'] ? '₹' . number_format((float)$pr['market_price'], 2) : '—' ?></td>
                                    <td><?= $pr['negotiated_price'] ? '₹' . number_format((float)$pr['negotiated_price'], 2) : '—' ?></td>
                                    <td><?= (float)$pr['discount'] > 0 ? '₹' . number_format((float)$pr['discount'], 2) : '0.00' ?></td>
                                    <td style="font-size: 0.85rem; color: var(--slate-600);">
                                        <?= esc($pr['effective_from'] ?: 'Immediate') ?>
                                        <?= $pr['effective_to'] ? ' to ' . esc($pr['effective_to']) : '' ?>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--slate-600); max-width: 200px;">
                                        <?= esc($pr['remarks'] ?: '—') ?>
                                    </td>
                                    <td style="font-size: 0.8rem; color: var(--slate-500); white-space: nowrap;">
                                        <?= esc(date('M d, Y', strtotime($pr['created_at']))) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?php if (session()->get('is_super_admin') || in_array('pricing.delete', session()->get('permissions') ?? [])): ?>
                                            <form action="/pricing/delete/<?= esc($pr['id']) ?>" method="POST" style="display: inline;" onsubmit="return confirm('Delete this pricing record?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm" style="color: var(--danger); background: #fee2e2; border-color: #fecaca;">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tab 6: Status History -->
<div id="tab-statusHistory" class="tab-content" style="display: none;">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="card-title">Availability Status Log</h2>
                <div class="card-subtitle">Complete chronological history of availability transitions</div>
            </div>
            <?php if (session()->get('is_super_admin') || in_array('availability.edit', session()->get('permissions') ?? [])): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modalChangeStatus').style.display='block'">
                    Update Status
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Previous Status</th>
                            <th>Transition</th>
                            <th>New Status</th>
                            <th>Changed By</th>
                            <th>Remarks / Reason</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($statusHistory)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                                    No status changes recorded yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($statusHistory as $sh): ?>
                                <tr>
                                    <td>#<?= esc($sh['id']) ?></td>
                                    <td>
                                        <span class="badge" style="background: var(--slate-100); color: var(--slate-700);">
                                            <?= esc($sh['old_status']) ?>
                                        </span>
                                    </td>
                                    <td style="color: var(--slate-400);">&rarr;</td>
                                    <td>
                                        <?= \App\Libraries\PropertyStatus::renderBadge($sh['new_status']) ?>
                                    </td>
                                    <td style="font-weight: 600; color: var(--slate-800);">
                                        <?= esc($sh['user_name'] ?? 'System') ?>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--slate-600); max-width: 280px;">
                                        <?= esc($sh['remarks'] ?: '—') ?>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--slate-500); white-space: nowrap;">
                                        <?= esc(date('M d, Y h:i A', strtotime($sh['created_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Change Status -->
<div id="modalChangeStatus" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; max-width: 500px; width: 90%; margin: 10% auto; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--slate-200); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Update Property Availability</h3>
            <button type="button" onclick="document.getElementById('modalChangeStatus').style.display='none'" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form action="/availability/property" method="POST" style="padding: 1.25rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="property_id" value="<?= esc($property['id']) ?>">

            <div class="form-group">
                <label class="form-label">Current Status</label>
                <div><?= \App\Libraries\PropertyStatus::renderBadge($property['status']) ?></div>
            </div>

            <div class="form-group">
                <label for="new_status" class="form-label required">Select New Status</label>
                <select name="status" id="new_status" class="form-control" required>
                    <?php foreach ($statuses as $st): ?>
                        <option value="<?= esc($st) ?>" <?= $property['status'] === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status_remarks" class="form-label">Remarks / Operational Note</label>
                <textarea name="remarks" id="status_remarks" rows="3" class="form-control" placeholder="Specify reason for availability state change..."></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalChangeStatus').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Pricing Schedule -->
<div id="modalAddPricing" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; max-width: 600px; width: 90%; margin: 6% auto; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--slate-200); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Record Pricing & Valuation</h3>
            <button type="button" onclick="document.getElementById('modalAddPricing').style.display='none'" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form action="/pricing/store" method="POST" style="padding: 1.25rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="property_id" value="<?= esc($property['id']) ?>">

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="base_price" class="form-label required">Base Price (₹)</label>
                        <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="<?= esc($property['price']) ?>" required>
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="market_price" class="form-label">Market Valuation (₹)</label>
                        <input type="number" step="0.01" name="market_price" id="market_price" class="form-control" placeholder="Estimated market price">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="negotiated_price" class="form-label">Negotiated Price (₹)</label>
                        <input type="number" step="0.01" name="negotiated_price" id="negotiated_price" class="form-control" placeholder="Target deal price">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="discount" class="form-label">Rebate / Discount (₹)</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0.00">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col-6">
                    <div class="form-group">
                        <label for="effective_from" class="form-label">Effective From</label>
                        <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-col-6">
                    <div class="form-group">
                        <label for="effective_to" class="form-label">Effective To</label>
                        <input type="date" name="effective_to" id="effective_to" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="pricing_remarks" class="form-label">Valuation Remarks</label>
                <textarea name="remarks" id="pricing_remarks" rows="2" class="form-control" placeholder="Notes on rate revision, seasonal incentive, etc."></textarea>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                    <input type="checkbox" name="update_primary_price" value="1" checked>
                    <span>Update active listing price with this Base Price</span>
                </label>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalAddPricing').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Pricing</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Upload Media -->
<div id="modalUploadMedia" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; max-width: 550px; width: 90%; margin: 8% auto; border-radius: 8px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--slate-200); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Upload Property Media & Document</h3>
            <button type="button" onclick="document.getElementById('modalUploadMedia').style.display='none'" style="border: none; background: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form action="/media/upload" method="POST" enctype="multipart/form-data" style="padding: 1.25rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="property_id" value="<?= esc($property['id']) ?>">

            <div class="form-group">
                <label for="media_type" class="form-label required">Media Type</label>
                <select name="media_type" id="media_type" class="form-control" required>
                    <option value="photo">Property Photo</option>
                    <option value="gallery">Gallery Image</option>
                    <option value="floor_plan">Floor Plan</option>
                    <option value="brochure">Brochure</option>
                    <option value="video">Video</option>
                    <option value="virtual_tour">Virtual Tour</option>
                    <option value="document">Legal / Title Document</option>
                </select>
            </div>

            <div class="form-group">
                <label for="media_file" class="form-label required">Select File</label>
                <input type="file" name="media_file" id="media_file" class="form-control" required>
                <div class="form-hint">Supported: JPG, PNG, WEBP, PDF, MP4, DOC, DOCX. Max 20MB.</div>
            </div>

            <div class="form-group">
                <label for="media_title" class="form-label">Asset Title</label>
                <input type="text" name="title" id="media_title" class="form-control" placeholder="e.g. Master Bedroom Sea View, Layout Plan">
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
                    <input type="checkbox" name="is_primary" value="1">
                    <span>Set as primary listing cover image</span>
                </label>
            </div>

            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalUploadMedia').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload Now</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('#propTabs .tab-btn').forEach(el => el.classList.remove('active'));
    
    const target = document.getElementById('tab-' + tabName);
    if (target) {
        target.style.display = 'block';
    }
    if (btn) {
        btn.classList.add('active');
    }
}
</script>

<style>
.tab-btn {
    background: none;
    border: none;
    padding: 0.75rem 1rem;
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--slate-500);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s ease;
}
.tab-btn:hover {
    color: var(--slate-800);
}
.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}
</style>
<?= $this->endSection() ?>
