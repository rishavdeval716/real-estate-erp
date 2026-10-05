<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <a href="/expenses">Property Expenses</a> &rsaquo;
            <span>Annual Statement</span>
        </div>
        <h1 class="page-title">Annual Property Expense Report (<?= esc($year) ?>)</h1>
        <p class="page-subtitle">Itemized category breakdown, monthly cash outflows, and tax analytics derived directly from MySQL.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.4rem;"><line x1="6" x2="6" y1="2" y2="22"/><line x1="18" x2="18" y1="2" y2="22"/><rect width="20" height="16" x="2" y="4" rx="2"/></svg>
            Print Report
        </button>
        <a href="/expenses" class="btn btn-secondary">Back to Expenses</a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/expenses/report" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Financial Year</label>
                <select name="year" class="form-control">
                    <?php for ($y = date('Y') + 1; $y >= 2024; $y--): ?>
                    <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div style="flex: 2; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Filter By Property</label>
                <select name="property_id" class="form-control">
                    <option value="">— System-Wide (All Properties) —</option>
                    <?php foreach ($properties as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= $propertyId == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Apply Filter</button>
        </form>
    </div>
</div>

<!-- Monthly Outflows Grid -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Monthly Expenditure Trend</h3>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; text-align: center;">
            <?php 
            $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
            foreach ($monthNames as $num => $name): 
                $val = $months[$num] ?? 0;
            ?>
            <div style="background: var(--slate-50); border: 1px solid var(--slate-200); padding: 0.85rem 0.5rem; border-radius: 8px;">
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--slate-500);"><?= $name ?></div>
                <div style="font-size: 1.05rem; font-weight: 700; color: <?= $val > 0 ? 'var(--danger)' : 'var(--slate-400)' ?>; margin-top: 0.25rem;">
                    ₹<?= number_format($val, 0) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Category Breakdown Table -->
<div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--slate-200); padding: 1rem 1.25rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Expenditure By Category</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Expense Category</th>
                        <th style="text-align: center;">Vouchers Count</th>
                        <th style="text-align: right;">Net Subtotal</th>
                        <th style="text-align: right;">Input Taxes / GST</th>
                        <th style="text-align: right;">Total Outlay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandSub = 0; $grandTax = 0; $grandTot = 0; $grandCnt = 0;
                    if (empty($categorySummary)): 
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--slate-500);">No expenses recorded for the selected criteria.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($categorySummary as $cs): 
                        $grandSub += (float)$cs['subtotal'];
                        $grandTax += (float)$cs['tax'];
                        $grandTot += (float)$cs['total'];
                        $grandCnt += (int)$cs['count'];
                    ?>
                    <tr>
                        <td><strong><?= esc($cs['category_name']) ?></strong></td>
                        <td style="text-align: center;"><span class="badge badge-info"><?= $cs['count'] ?></span></td>
                        <td style="text-align: right;">₹<?= number_format((float)$cs['subtotal'], 2) ?></td>
                        <td style="text-align: right;">₹<?= number_format((float)$cs['tax'], 2) ?></td>
                        <td style="text-align: right;"><strong style="color: var(--danger);">₹<?= number_format((float)$cs['total'], 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: var(--slate-50); font-weight: 800; font-size: 1rem;">
                        <td>TOTALS</td>
                        <td style="text-align: center;"><?= $grandCnt ?> Vouchers</td>
                        <td style="text-align: right;">₹<?= number_format($grandSub, 2) ?></td>
                        <td style="text-align: right;">₹<?= number_format($grandTax, 2) ?></td>
                        <td style="text-align: right; color: var(--danger);">₹<?= number_format($grandTot, 2) ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
