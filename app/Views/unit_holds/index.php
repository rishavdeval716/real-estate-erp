<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/leads">CRM</a> &rsaquo;
            <span>Unit Holds</span>
        </div>
        <h1 class="page-title">Temporary Unit Holds & Token Reservations</h1>
        <p class="page-subtitle">Manage time-limited property reservations during token discussion without finalizing full sales bookings.</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="metric-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.5rem;">
    <a href="/unit-holds?status=Active" style="text-decoration: none; color: inherit;">
        <div class="metric-card warning" style="<?= $status === 'Active' ? 'outline: 2px solid var(--warning);' : '' ?>">
            <div>
                <div class="metric-label">Active Holds</div>
                <div class="metric-value"><?= number_format($kpi['active']) ?></div>
                <div class="metric-meta"><span>Units currently reserved</span></div>
            </div>
            <div class="metric-icon-box warning">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
            </div>
        </div>
    </a>
    <a href="/unit-holds?status=Expired" style="text-decoration: none; color: inherit;">
        <div class="metric-card" style="border-left: 4px solid var(--danger); <?= $status === 'Expired' ? 'outline: 2px solid var(--danger);' : '' ?>">
            <div>
                <div class="metric-label">Expired Holds</div>
                <div class="metric-value" style="color: var(--danger);"><?= number_format($kpi['expired']) ?></div>
                <div class="metric-meta"><span>Auto-released to inventory</span></div>
            </div>
        </div>
    </a>
    <a href="/unit-holds?status=Released" style="text-decoration: none; color: inherit;">
        <div class="metric-card info" style="<?= $status === 'Released' ? 'outline: 2px solid var(--info);' : '' ?>">
            <div>
                <div class="metric-label">Manually Released</div>
                <div class="metric-value"><?= number_format($kpi['released']) ?></div>
                <div class="metric-meta"><span>Returned to available</span></div>
            </div>
        </div>
    </a>
    <a href="/unit-holds?status=all" style="text-decoration: none; color: inherit;">
        <div class="metric-card primary" style="<?= $status === 'all' ? 'outline: 2px solid var(--primary);' : '' ?>">
            <div>
                <div class="metric-label">Total Reservations</div>
                <div class="metric-value"><?= number_format($kpi['total']) ?></div>
                <div class="metric-meta"><span>Hold transactions log</span></div>
            </div>
        </div>
    </a>
</div>

<!-- Filter Tabs -->
<div class="tabs-nav" style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--slate-200); margin-bottom: 1.5rem;">
    <a href="/unit-holds?status=Active" class="tab-btn <?= $status === 'Active' ? 'active' : '' ?>">Active Holds (<?= $kpi['active'] ?>)</a>
    <a href="/unit-holds?status=Expired" class="tab-btn <?= $status === 'Expired' ? 'active' : '' ?>">Expired (<?= $kpi['expired'] ?>)</a>
    <a href="/unit-holds?status=Released" class="tab-btn <?= $status === 'Released' ? 'active' : '' ?>">Released (<?= $kpi['released'] ?>)</a>
    <a href="/unit-holds?status=all" class="tab-btn <?= $status === 'all' ? 'active' : '' ?>">All Records</a>
</div>

<!-- Holds Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Hold Code</th>
                    <th>Reserved Unit</th>
                    <th>Property / Project</th>
                    <th>Lead Name / Contact</th>
                    <th>Held By</th>
                    <th>Time Window</th>
                    <th>Status</th>
                    <th>Reason / Notes</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($holds)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--slate-400); padding: 3rem;">
                            No unit holds currently registered in this view.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($holds as $h): ?>
                        <tr>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: var(--primary); font-size: 0.82rem;">
                                    <?= esc($h['hold_code']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--slate-900); font-size: 0.95rem;">
                                    Unit <?= esc($h['unit_number']) ?>
                                </div>
                                <div style="font-size: 0.76rem; color: var(--slate-500);">
                                    <?= esc($h['flat_type'] ?: 'Standard') ?> &bull; ₹<?= number_format($h['unit_price'] ?? 0) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--slate-800);">
                                    <?= esc($h['property_title'] ?: 'General Development') ?>
                                </div>
                                <div style="font-size: 0.72rem; color: var(--slate-400); font-family: monospace;">
                                    <?= esc($h['property_code']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--slate-900);">
                                    <a href="/leads/view/<?= esc($h['lead_id']) ?>" style="color: inherit; text-decoration: none;">
                                        <?= esc($h['first_name'] . ' ' . $h['last_name']) ?>
                                    </a>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--slate-500);">
                                    <?= esc($h['lead_code']) ?> &bull; <?= esc($h['lead_phone']) ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--slate-700);"><?= esc($h['held_by_name'] ?? 'System') ?></span>
                            </td>
                            <td>
                                <div style="font-size: 0.78rem; color: var(--slate-700);">
                                    <strong>From:</strong> <?= date('d M, h:i A', strtotime($h['started_at'])) ?>
                                </div>
                                <div style="font-size: 0.78rem; color: <?= (strtotime($h['expires_at']) < time() && $h['hold_status'] === 'Active') ? 'var(--danger)' : 'var(--slate-700)' ?>;">
                                    <strong>Until:</strong> <?= date('d M, h:i A', strtotime($h['expires_at'])) ?>
                                </div>
                                <?php if ($h['hold_status'] === 'Active'): ?>
                                    <?php 
                                    $diffSec = strtotime($h['expires_at']) - time();
                                    $hrsLeft = round($diffSec / 3600, 1);
                                    ?>
                                    <div style="font-size: 0.72rem; color: #b45309; font-weight: 700; margin-top: 0.15rem;">
                                        <?= $hrsLeft > 0 ? "Expires in ~{$hrsLeft}h" : "Expired" ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badgeStyle = match($h['hold_status']) {
                                    'Active'    => 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;',
                                    'Released'  => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
                                    'Expired'   => 'background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;',
                                    'Converted' => 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;',
                                    default     => 'background: #f1f5f9; color: #475569;'
                                };
                                ?>
                                <span style="display: inline-flex; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; <?= $badgeStyle ?>">
                                    <?= esc($h['hold_status']) ?>
                                </span>
                            </td>
                            <td style="max-width: 200px; font-size: 0.8rem; color: var(--slate-600);">
                                <div style="font-weight: 600; color: var(--slate-800);"><?= esc($h['hold_reason']) ?></div>
                                <?php if ($h['remarks']): ?>
                                    <div style="font-size: 0.74rem; color: var(--slate-500); margin-top: 0.15rem; white-space: pre-wrap;"><?= esc($h['remarks']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($h['hold_status'] === 'Active'): ?>
                                    <button type="button" class="btn btn-sm" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 600;" onclick="openReleaseModal(<?= esc($h['id']) ?>, '<?= esc($h['hold_code']) ?>', '<?= esc($h['unit_number']) ?>')">
                                        Release Hold
                                    </button>
                                <?php else: ?>
                                    <a href="/leads/view/<?= esc($h['lead_id']) ?>" class="btn btn-sm btn-secondary">Lead File</a>
                                <?php endif; ?>
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

<!-- Modal: Release Hold -->
<div id="modal-release-hold" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 460px; margin: 1rem;">
        <h3 style="margin-top: 0; font-size: 1.15rem; font-weight: 700; color: var(--slate-900);">Release Unit Reservation Hold</h3>
        <p style="font-size: 0.85rem; color: var(--slate-600); margin-bottom: 1rem;">
            Releasing <strong id="release-hold-code" style="color: var(--primary);"></strong> will immediately return Unit <strong id="release-unit-num"></strong> to <strong>Available</strong> status for all sales agents.
        </p>
        <form id="form-release-hold" method="post" action="">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Release Reason <span style="color: var(--danger);">*</span></label>
                <textarea name="release_reason" class="form-control" rows="3" required placeholder="e.g. Negotiation discontinued / client chose alternative property / token timeout"></textarea>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeHoldModal()">Cancel</button>
                <button type="submit" class="btn" style="background: var(--danger); color: #fff;">Confirm & Release Unit</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReleaseModal(id, code, unitNum) {
    document.getElementById('form-release-hold').action = '/unit-holds/release/' + id;
    document.getElementById('release-hold-code').textContent = code;
    document.getElementById('release-unit-num').textContent = unitNum;
    document.getElementById('modal-release-hold').style.display = 'flex';
}

function closeHoldModal() {
    document.getElementById('modal-release-hold').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
