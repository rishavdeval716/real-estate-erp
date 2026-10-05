<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/reports/tds">Compliance & Reports</a> &rsaquo;
            <span>TDS Management</span>
        </div>
        <h1 class="page-title">Tax Deducted at Source (TDS) & Form 16A</h1>
        <p class="page-subtitle">Track Section 194H broker commission withholding, statutory quarter returns, and Form 16A certificates.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-primary" onclick="openTdsModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Record TDS Deduction
        </button>
    </div>
</div>

<!-- Dynamic KPI Metrics Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total Deductions Count</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);"><?= esc($kpi['count']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Gross Commission Base</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--slate-800);">₹<?= number_format((float)$kpi['total_gross'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Total TDS Withheld (Sec 194H)</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--rose-600);">₹<?= number_format((float)$kpi['total_tds'], 2) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem;">
        <div style="font-size: 0.85rem; color: var(--slate-500); margin-bottom: 0.25rem;">Net Payout to Brokers</div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--emerald-600);">₹<?= number_format((float)$kpi['total_net'], 2) ?></div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/reports/tds" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="min-width: 160px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Financial Year</label>
                <select name="financial_year" class="form-control">
                    <option value="2026-2027" <?= $filters['financial_year'] === '2026-2027' ? 'selected' : '' ?>>FY 2026-2027</option>
                    <option value="2025-2026" <?= $filters['financial_year'] === '2025-2026' ? 'selected' : '' ?>>FY 2025-2026</option>
                </select>
            </div>
            <div style="min-width: 140px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Quarter</label>
                <select name="quarter" class="form-control">
                    <option value="">All Quarters</option>
                    <option value="Q1" <?= $filters['quarter'] === 'Q1' ? 'selected' : '' ?>>Q1 (Apr - Jun)</option>
                    <option value="Q2" <?= $filters['quarter'] === 'Q2' ? 'selected' : '' ?>>Q2 (Jul - Sep)</option>
                    <option value="Q3" <?= $filters['quarter'] === 'Q3' ? 'selected' : '' ?>>Q3 (Oct - Dec)</option>
                    <option value="Q4" <?= $filters['quarter'] === 'Q4' ? 'selected' : '' ?>>Q4 (Jan - Mar)</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="/reports/tds" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- TDS Entries Table -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Section 194H Broker Commission TDS Register</h3>
        <span class="badge badge-light"><?= count($entries) ?> Entries</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Entry Code</th>
                        <th>Deductee / Broker</th>
                        <th>PAN Number</th>
                        <th>Section</th>
                        <th>Deduction Date</th>
                        <th>Gross Amount</th>
                        <th>TDS (5%)</th>
                        <th>Net Payable</th>
                        <th>Status</th>
                        <th style="text-align: right;">Form 16A Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2.5rem; color: var(--slate-400);">No TDS deduction records found for the selected period.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($entries as $e): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($e['entry_code']) ?></strong>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= esc($e['party_name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--slate-500);"><?= esc($e['transaction_reference']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-light" style="font-family: monospace; font-size: 0.85rem;"><?= esc($e['pan_number']) ?></span>
                        </td>
                        <td><?= esc($e['section']) ?></td>
                        <td><?= date('d M Y', strtotime($e['deduction_date'])) ?></td>
                        <td>₹<?= number_format((float)$e['gross_amount'], 2) ?></td>
                        <td>
                            <strong style="color: var(--rose-600);">₹<?= number_format((float)$e['tds_amount'], 2) ?></strong>
                        </td>
                        <td>
                            <strong style="color: var(--emerald-600);">₹<?= number_format((float)$e['net_payable'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="badge <?= $e['status'] === 'certified' ? 'badge-success' : 'badge-info' ?>" style="text-transform: capitalize;">
                                <?= esc($e['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <?php if ($e['status'] !== 'certified'): ?>
                            <form method="POST" action="/reports/tds/certificate/<?= $e['id'] ?>" onsubmit="return confirm('Generate statutory Form 16A TDS certificate for <?= esc($e['party_name']) ?>?');" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Issue Form 16A
                                </button>
                            </form>
                            <?php else: ?>
                            <span style="font-size: 0.8rem; color: var(--emerald-600); font-weight: 600;">&check; Certified</span>
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

<!-- Issued Form 16A Certificates Table -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Statutory Form 16A Certificates Issued</h3>
        <span class="badge badge-light"><?= count($certificates) ?> Certificates</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Certificate #</th>
                        <th>Deductee Party</th>
                        <th>PAN Number</th>
                        <th>Financial Year</th>
                        <th>Quarter</th>
                        <th>Gross Value</th>
                        <th>TDS Amount</th>
                        <th>Date of Issue</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--slate-400);">No Form 16A certificates issued yet.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($cert['certificate_number']) ?></strong>
                        </td>
                        <td><?= esc($cert['party_name']) ?></td>
                        <td><span class="badge badge-light" style="font-family: monospace;"><?= esc($cert['pan_number']) ?></span></td>
                        <td><?= esc($cert['financial_year']) ?></td>
                        <td><?= esc($cert['quarter']) ?></td>
                        <td>₹<?= number_format((float)$cert['gross_amount'], 2) ?></td>
                        <td>
                            <strong style="color: var(--rose-600);">₹<?= number_format((float)$cert['tds_amount'], 2) ?></strong>
                        </td>
                        <td><?= date('d M Y', strtotime($cert['issue_date'])) ?></td>
                        <td style="text-align: right;">
                            <span class="badge badge-success">Valid Issued</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Record TDS Deduction -->
<div id="tdsModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 999; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 550px; margin: 1rem; border-radius: 8px;">
        <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Record Section 194H TDS Deduction</h3>
            <button type="button" onclick="closeTdsModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--slate-400);">&times;</button>
        </div>
        <form method="POST" action="/reports/tds/store">
            <?= csrf_field() ?>
            <div class="card-body" style="padding: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Deductee / Broker Name <span style="color: var(--rose-500);">*</span></label>
                    <input type="text" name="party_name" class="form-control" placeholder="e.g. Skyline Realty Advisory Pvt Ltd" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">10-Digit PAN Number <span style="color: var(--rose-500);">*</span></label>
                        <input type="text" name="pan_number" class="form-control" placeholder="e.g. ABCDE1234F" style="text-transform: uppercase;" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Income Tax Section</label>
                        <input type="text" name="section" class="form-control" value="194H" readonly>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Gross Commission (₹) <span style="color: var(--rose-500);">*</span></label>
                        <input type="number" step="0.01" name="gross_amount" class="form-control" placeholder="e.g. 50000.00" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">TDS Rate (%) <span style="color: var(--rose-500);">*</span></label>
                        <input type="number" step="0.01" name="tds_rate" class="form-control" value="5.00" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 500;">Deduction Date <span style="color: var(--rose-500);">*</span></label>
                        <input type="date" name="deduction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Financial Year <span style="color: var(--rose-500);">*</span></label>
                        <select name="financial_year" class="form-control" required>
                            <option value="2026-2027">2026-2027</option>
                            <option value="2025-2026">2025-2026</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 500;">Quarter <span style="color: var(--rose-500);">*</span></label>
                        <select name="quarter" class="form-control" required>
                            <option value="Q1">Q1</option>
                            <option value="Q2">Q2</option>
                            <option value="Q3" selected>Q3</option>
                            <option value="Q4">Q4</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-weight: 500;">Payout / Booking Reference</label>
                    <input type="text" name="transaction_reference" class="form-control" placeholder="e.g. Brokerage on Booking BK-2026-000001">
                </div>
            </div>
            <div class="card-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--slate-200);">
                <button type="button" onclick="closeTdsModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Record TDS</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTdsModal() {
    document.getElementById('tdsModal').style.display = 'flex';
}
function closeTdsModal() {
    document.getElementById('tdsModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
