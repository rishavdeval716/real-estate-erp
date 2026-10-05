<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 24px; font-weight: 700; color: var(--slate-900); margin-bottom: 4px;"><?= esc($title) ?></h1>
        <p style="color: var(--slate-500); font-size: 14px;">Customer possession clearance, electricity & water meter handovers, and official certificate generation</p>
    </div>
    <div>
        <a href="/handover/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            Execute Unit Handover
        </a>
    </div>
</div>

<!-- Handovers Table -->
<div class="erp-card">
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Certificate #</th>
                        <th>Handover Date</th>
                        <th>Customer / Buyer</th>
                        <th>Booked Property Unit</th>
                        <th>Financial Clearance</th>
                        <th>Snagging Clearance</th>
                        <th>Meter Handover</th>
                        <th>Keys Provided</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($handovers)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--slate-500); padding: 32px;">No possession handovers recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($handovers as $h): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary);"><?= esc($h['certificate_number']) ?></td>
                                <td><strong><?= date('d M Y', strtotime($h['handover_date'])) ?></strong></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--slate-900);"><?= esc($h['first_name'] . ' ' . $h['last_name']) ?></div>
                                    <div style="font-size: 12px; color: var(--slate-500);"><?= esc($h['phone']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">Unit <?= esc($h['unit_number']) ?></div>
                                    <div style="font-size: 11px; color: var(--slate-500);"><?= esc($h['project_name']) ?> (<?= esc($h['tower_name'] ?? 'Tower') ?>)</div>
                                </td>
                                <td>
                                    <?php if ($h['financial_clearance']): ?>
                                        <span class="badge badge-success">✓ 100% Paid</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($h['snagging_clearance']): ?>
                                        <span class="badge badge-success">✓ Zero Snags</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">In Review</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 12px;">
                                    <div>Elec: <?= esc($h['electricity_meter_number'] ?: 'Recorded') ?> (<?= $h['initial_electricity_reading'] ?> kWh)</div>
                                    <div>Water: <?= esc($h['water_meter_number'] ?: 'Recorded') ?> (<?= $h['initial_water_reading'] ?> kL)</div>
                                </td>
                                <td><strong><?= esc($h['key_sets_provided']) ?> Sets</strong></td>
                                <td><span class="badge badge-success"><?= esc($h['status']) ?></span></td>
                                <td style="text-align: right;">
                                    <a href="/handover/certificate/<?= $h['id'] ?>" class="btn btn-sm btn-outline-primary" style="display: inline-flex; align-items: center; gap: 4px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        Print Certificate
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
