<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/reports/gst">Compliance & Reports</a> &rsaquo;
            <span>GST Reports</span>
        </div>
        <h1 class="page-title">GST Statutory Filing Reports (GSTR-1 & GSTR-3B)</h1>
        <p class="page-subtitle">Monthly outward supplies summary, tax liabilities, CGST & SGST distribution across sales and commercial leases.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Print Tax Report
        </button>
    </div>
</div>

<!-- Month Selector Card -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/reports/gst" style="display: flex; gap: 1rem; align-items: flex-end;">
            <div style="min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Select Filing Month</label>
                <input type="month" name="month" class="form-control" value="<?= esc($month) ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                Generate Monthly Return
            </button>
        </form>
    </div>
</div>

<!-- GSTR-3B Consolidated Summary Grid -->
<div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, var(--slate-900), var(--slate-800)); color: white;">
    <div class="card-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: white;">Table 3.1 &mdash; Consolidated Outward Taxable Supplies Summary</h3>
        <span class="badge badge-light" style="font-weight: 700;">Period: <?= date('F Y', strtotime($month . '-01')) ?></span>
    </div>
    <div class="card-body" style="padding: 1.5rem 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-size: 0.8rem; color: var(--slate-400); text-transform: uppercase;">Total Taxable Turnover</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: white; margin-top: 0.25rem;">
                    ₹<?= number_format((float)$kpi['total_taxable'], 2) ?>
                </div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--slate-400); text-transform: uppercase;">Total Output Tax Liability</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #f59e0b; margin-top: 0.25rem;">
                    ₹<?= number_format((float)$kpi['total_tax'], 2) ?>
                </div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--slate-400); text-transform: uppercase;">Central GST (CGST 9%)</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #38bdf8; margin-top: 0.25rem;">
                    ₹<?= number_format((float)$kpi['cgst'], 2) ?>
                </div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--slate-400); text-transform: uppercase;">State GST (SGST 9%)</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: #34d399; margin-top: 0.25rem;">
                    ₹<?= number_format((float)$kpi['sgst'], 2) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GSTR-1 Sales Invoices Section -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">GSTR-1: Property Sales Tax Invoices (B2B & B2C)</h3>
        <span class="badge badge-light"><?= count($invoices) ?> Invoices</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Invoice Date</th>
                        <th>Customer Name</th>
                        <th>Booking #</th>
                        <th>Taxable Value</th>
                        <th>CGST (9%)</th>
                        <th>SGST (9%)</th>
                        <th>Total Invoice</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--slate-400);">No sales invoices registered in <?= date('F Y', strtotime($month . '-01')) ?>.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--primary-600);"><?= esc($inv['invoice_number']) ?></strong>
                        </td>
                        <td><?= date('d M Y', strtotime($inv['invoice_date'])) ?></td>
                        <td><?= esc($inv['first_name'] . ' ' . $inv['last_name']) ?></td>
                        <td><?= esc($inv['booking_number']) ?></td>
                        <td>₹<?= number_format((float)$inv['subtotal'], 2) ?></td>
                        <td>₹<?= number_format((float)$inv['tax_amount'] / 2, 2) ?></td>
                        <td>₹<?= number_format((float)$inv['tax_amount'] / 2, 2) ?></td>
                        <td>
                            <strong>₹<?= number_format((float)$inv['total_amount'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-success"><?= esc($inv['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Commercial Rent Demands GST -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Commercial Tenancy GST Supplies (18% SAC 9972)</h3>
        <span class="badge badge-light"><?= count($demands) ?> Demands</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Demand Notice #</th>
                        <th>Tenant</th>
                        <th>Due Date</th>
                        <th>Base Rent & CAM</th>
                        <th>CGST (9%)</th>
                        <th>SGST (9%)</th>
                        <th>Total Tax</th>
                        <th>Gross Demand</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($demands)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--slate-400);">No commercial rent demand supplies recorded in <?= date('F Y', strtotime($month . '-01')) ?>.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($demands as $d): ?>
                    <tr>
                        <td>
                            <strong><?= esc($d['demand_number']) ?></strong>
                        </td>
                        <td><?= esc($d['tenant_name']) ?></td>
                        <td><?= date('d M Y', strtotime($d['due_date'])) ?></td>
                        <td>₹<?= number_format((float)$d['base_rent'] + (float)$d['maintenance'] + (float)$d['other_charges'], 2) ?></td>
                        <td>₹<?= number_format((float)$d['tax'] / 2, 2) ?></td>
                        <td>₹<?= number_format((float)$d['tax'] / 2, 2) ?></td>
                        <td style="color: var(--indigo-600); font-weight: 600;">₹<?= number_format((float)$d['tax'], 2) ?></td>
                        <td>
                            <strong>₹<?= number_format((float)$d['total_amount'], 2) ?></strong>
                        </td>
                        <td>
                            <span class="badge <?= $d['status'] === 'paid' ? 'badge-success' : 'badge-warning' ?>"><?= esc($d['status']) ?></span>
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
