<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/commissions">Sales</a> &rsaquo;
            <span>Commissions</span>
        </div>
        <h1 class="page-title">Broker & Agent Commissions</h1>
        <p class="page-subtitle">Configure commission rules, calculate agent payouts, and track disbursements.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('ruleModal').style.display='block';">
            + New Commission Rule
        </button>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('assignModal').style.display='block';">
            + Calculate Payout
        </button>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<!-- Commission Rules Table -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Active Commission Rules</h3>
        <span style="font-size: 0.8rem; color: var(--slate-500);">Predefined percentage and fixed payout schemes</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rule Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Applicable To</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rules)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--slate-500);">
                                No commission rules configured.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rules as $rl): ?>
                            <tr>
                                <td style="font-weight: 600;"><?= esc($rl['name']) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($rl['commission_type']) ?></span></td>
                                <td style="font-weight: 700; color: var(--brand-primary);">
                                    <?= $rl['commission_type'] === 'Percentage' ? esc($rl['commission_value']) . '%' : '₹' . number_format($rl['commission_value'], 2) ?>
                                </td>
                                <td><?= esc($rl['applicable_to']) ?></td>
                                <td>
                                    <span class="badge <?= $rl['status'] === 'Active' ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= esc($rl['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Commission Records Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Commission Payout Ledgers</h3>
        <form method="GET" action="/commissions" style="display: flex; gap: 0.5rem;">
            <select name="status" class="form-control" style="font-size: 0.8rem; padding: 0.3rem 0.5rem;" onchange="this.form.submit();">
                <option value="">All Statuses</option>
                <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Approved" <?= $filters['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Paid" <?= $filters['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </form>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking #</th>
                        <th>Agent / Broker</th>
                        <th>Scheme</th>
                        <th>Booking Value</th>
                        <th>Commission (INR)</th>
                        <th>Payable Date</th>
                        <th>Paid Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--slate-500);">
                                No commission ledger entries found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commissions as $cm): ?>
                            <tr>
                                <td>
                                    <a href="/bookings/view/<?= $cm['booking_id'] ?>" style="font-family: monospace; font-weight: 700; color: var(--brand-primary); text-decoration: none;">
                                        <?= esc($cm['booking_number']) ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($cm['agent_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($cm['agent_email']) ?></div>
                                </td>
                                <td style="font-size: 0.85rem;"><?= esc($cm['rule_name'] ?: 'Direct Custom') ?></td>
                                <td style="font-weight: 600;">₹<?= number_format($cm['booking_amount'], 2) ?></td>
                                <td style="font-weight: 700; color: #16a34a; font-size: 0.95rem;">
                                    ₹<?= number_format($cm['commission_amount'], 2) ?>
                                </td>
                                <td style="font-size: 0.85rem;"><?= $cm['payable_date'] ? date('d M Y', strtotime($cm['payable_date'])) : '-' ?></td>
                                <td style="font-size: 0.85rem; color: var(--slate-600);"><?= $cm['paid_date'] ? date('d M Y', strtotime($cm['paid_date'])) : 'Pending' ?></td>
                                <td>
                                    <?php
                                    $stBadge = 'badge-secondary';
                                    if ($cm['status'] === 'Paid') $stBadge = 'badge-success';
                                    elseif ($cm['status'] === 'Approved') $stBadge = 'badge-primary';
                                    elseif ($cm['status'] === 'Pending') $stBadge = 'badge-warning';
                                    elseif ($cm['status'] === 'Cancelled') $stBadge = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $stBadge ?>"><?= esc($cm['status']) ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                        <?php if ($cm['status'] === 'Pending'): ?>
                                            <form method="POST" action="/commissions/approve/<?= $cm['id'] ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                            </form>
                                        <?php elseif ($cm['status'] === 'Approved'): ?>
                                            <form method="POST" action="/commissions/mark-paid/<?= $cm['id'] ?>" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
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

        <?php if (!empty($pager)): ?>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--slate-200);">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: New Commission Rule -->
<div id="ruleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 480px; width: 90%; margin: 5% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Create Commission Rule</h3>
            <button type="button" onclick="document.getElementById('ruleModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/commissions/rule/store">
            <?= csrf_field() ?>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Rule Name <span style="color: red;">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Standard Broker 2%">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label class="form-label">Type <span style="color: red;">*</span></label>
                    <select name="commission_type" class="form-control" required>
                        <option value="Percentage">Percentage (%)</option>
                        <option value="Fixed">Fixed Amount (₹)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Value <span style="color: red;">*</span></label>
                    <input type="number" step="0.01" name="commission_value" class="form-control" required placeholder="e.g. 2.0 or 50000">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Applicable To <span style="color: red;">*</span></label>
                    <select name="applicable_to" class="form-control" required>
                        <option value="All">All Entities</option>
                        <option value="Broker">Broker</option>
                        <option value="Channel Partner">Channel Partner</option>
                        <option value="Direct">Direct In-house Sales</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status <span style="color: red;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('ruleModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Rule</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Assign Payout -->
<div id="assignModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; max-width: 500px; width: 90%; margin: 5% auto; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Calculate Agent Commission</h3>
            <button type="button" onclick="document.getElementById('assignModal').style.display='none';" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/commissions/store">
            <?= csrf_field() ?>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Select Booking <span style="color: red;">*</span></label>
                <select name="booking_id" class="form-control" required>
                    <option value="">-- Select Active Booking --</option>
                    <?php foreach ($bookings as $bk): ?>
                        <option value="<?= $bk['id'] ?>">
                            <?= esc($bk['booking_number']) ?> &bull; <?= esc($bk['first_name'] . ' ' . $bk['last_name']) ?> (₹<?= number_format($bk['final_amount'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Sales Executive / Agent <span style="color: red;">*</span></label>
                <select name="agent_user_id" class="form-control" required>
                    <?php foreach ($agents as $ag): ?>
                        <option value="<?= $ag['id'] ?>"><?= esc($ag['name']) ?> (<?= esc($ag['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Rule / Formula (Optional)</label>
                <select name="commission_rule_id" class="form-control">
                    <option value="">-- Custom Manual Amount --</option>
                    <?php foreach ($rules as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= esc($r['name']) ?> (<?= esc($r['commission_type']) ?>: <?= esc($r['commission_value']) ?><?= $r['commission_type'] === 'Percentage' ? '%' : '' ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Manual Amount (₹)</label>
                    <input type="number" step="0.01" name="commission_amount" class="form-control" placeholder="If not using rule">
                </div>
                <div>
                    <label class="form-label">Payable Due Date</label>
                    <input type="date" name="payable_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('assignModal').style.display='none';">Cancel</button>
                <button type="submit" class="btn btn-primary">Calculate & Save</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
