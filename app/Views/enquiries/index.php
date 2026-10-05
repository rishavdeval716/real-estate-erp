<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Enquiries</span>
        </div>
        <h1 class="page-title">Property Enquiries</h1>
        <p class="page-subtitle">Centralized log of client purchase, investment, and rental requirements.</p>
    </div>
</div>

<!-- Filters Card -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="get" action="/enquiries" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <input type="text" name="search" class="form-control" placeholder="Search by enquiry code, lead name, phone, requirement..." value="<?= esc($search) ?>">
        </div>
        <div style="width: 180px;">
            <select name="type" class="form-control">
                <option value="">All Types</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= esc($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= esc($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="width: 180px;">
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $st): ?>
                    <option value="<?= esc($st) ?>" <?= $status === $st ? 'selected' : '' ?>><?= esc($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if (!empty($search) || !empty($status) || !empty($type)): ?>
                <a href="/enquiries" class="btn btn-secondary" style="margin-left: 0.5rem;">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Enquiry Code</th>
                    <th>Lead / Prospect</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Property / Project</th>
                    <th>Budget</th>
                    <th>Requirement</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No enquiries found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $enq): ?>
                        <tr>
                            <td>
                                <code><?= esc($enq['enquiry_code']) ?></code>
                            </td>
                            <td>
                                <a href="/leads/view/<?= esc($enq['lead_id']) ?>" style="font-weight: 700; color: var(--slate-900); text-decoration: none;">
                                    <?= esc($enq['first_name'] . ' ' . $enq['last_name']) ?>
                                </a>
                                <div style="font-family: monospace; font-size: 0.75rem; color: var(--slate-500);">
                                    <?= esc($enq['lead_code']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; font-weight: 600;"><?= esc($enq['lead_phone']) ?></div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--primary);">
                                    <?= esc($enq['enquiry_type']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($enq['property_title'])): ?>
                                    <div style="font-weight: 600;"><?= esc($enq['property_title']) ?></div>
                                <?php elseif (!empty($enq['project_name'])): ?>
                                    <div style="font-weight: 600;"><?= esc($enq['project_name']) ?></div>
                                <?php else: ?>
                                    <span style="color: var(--slate-400);">General Portfolio</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((float)$enq['budget'] > 0): ?>
                                    <strong style="color: var(--slate-900);">₹<?= number_format((float)$enq['budget'], 2) ?></strong>
                                <?php else: ?>
                                    <span style="color: var(--slate-400);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 220px; font-size: 0.85rem; color: var(--slate-700);">
                                <?= esc($enq['requirement'] ?: '—') ?>
                            </td>
                            <td>
                                <span style="display: inline-flex; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff;">
                                    <?= esc($enq['status']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.82rem; color: var(--slate-500); white-space: nowrap;">
                                <?= date('d M Y', strtotime($enq['created_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <a href="/leads/view/<?= esc($enq['lead_id']) ?>" class="btn btn-sm btn-secondary">View Lead</a>
                                    <form method="post" action="/enquiries/delete/<?= esc($enq['id']) ?>" style="display: inline;" onsubmit="return confirm('Delete this enquiry record?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pager): ?>
        <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
