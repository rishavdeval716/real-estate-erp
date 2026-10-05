<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <a href="/dashboard">Administration</a>
            <span class="separator">/</span>
            <a href="/property-types">Property Types</a>
            <span class="separator">/</span>
            <span><?= esc($type['name']) ?></span>
        </div>
        <h1 class="page-title"><?= esc($type['name']) ?></h1>
        <p class="page-subtitle">Property category overview and assigned active listings</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('property_types.edit', session()->get('permissions') ?? [])): ?>
            <a href="/property-types/edit/<?= esc($type['id']) ?>" class="btn btn-primary">Edit Type</a>
        <?php endif; ?>
        <a href="/property-types" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="form-row">
    <div class="form-col-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Category Profile</h2>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-400); font-weight: 700;">Name</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--slate-900);"><?= esc($type['name']) ?></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-400); font-weight: 700;">Slug</div>
                    <code><?= esc($type['slug']) ?></code>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-400); font-weight: 700;">Status</div>
                    <span class="badge badge-<?= $type['status'] ?>">
                        <span class="badge-dot"></span>
                        <?= ucfirst(esc($type['status'])) ?>
                    </span>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--slate-400); font-weight: 700;">Description</div>
                    <div style="font-size: 0.88rem; color: var(--slate-600);"><?= esc($type['description'] ?: 'No description provided.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-col-8">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title">Properties Under This Type</h2>
                    <div class="card-subtitle">Active inventory listings categorized as <?= esc($type['name']) ?></div>
                </div>
                <span class="badge" style="background: var(--slate-100); color: var(--slate-700); font-weight: 700;">
                    <?= count($properties) ?> Total
                </span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Area (sq.ft)</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--slate-400); padding: 2.5rem;">
                                        No properties currently registered under this category.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($properties as $p): ?>
                                    <tr>
                                        <td><code><?= esc($p['property_code']) ?></code></td>
                                        <td style="font-weight: 600; color: var(--slate-900);"><?= esc($p['title']) ?></td>
                                        <td><?= number_format((float)$p['area'], 2) ?></td>
                                        <td style="font-weight: 700; color: var(--primary);">₹<?= number_format((float)$p['price'], 2) ?></td>
                                        <td>
                                            <?= \App\Libraries\PropertyStatus::renderBadge($p['status']) ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="/properties/view/<?= esc($p['id']) ?>" class="btn btn-sm btn-secondary">View</a>
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
</div>
<?= $this->endSection() ?>
