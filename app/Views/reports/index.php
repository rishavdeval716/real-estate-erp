<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <div class="breadcrumb">
            <a href="/dashboard">Dashboard</a> &rsaquo;
            <span>Executive Reports & Analytics</span>
        </div>
        <h1 class="page-title">Executive Reports & Analytics Hub</h1>
        <p class="page-subtitle">Centralized business intelligence, statutory compliance, revenue accounting, and operating metrics.</p>
    </div>
</div>

<!-- High-Level Financial Position Metrics -->
<div class="metric-grid" style="margin-bottom: 2rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Gross Bookings Value</div>
            <div class="metric-value" style="color: var(--primary);">₹<?= number_format($totalBookingsValue, 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Cumulative active sales pipeline</div>
        </div>
        <div class="metric-icon-box" style="background: var(--primary-light); color: var(--primary);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Actual Collections</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($totalCollected, 2) ?></div>
            <div class="metric-meta" style="color: var(--success);">Realized cash & bank inflows</div>
        </div>
        <div class="metric-icon-box" style="background: var(--success-light); color: var(--success);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Operating Expenses</div>
            <div class="metric-value" style="color: var(--danger);">₹<?= number_format($totalExpenses, 2) ?></div>
            <div class="metric-meta" style="color: var(--slate-500);">Maintenance, repairs & taxes</div>
        </div>
        <div class="metric-icon-box" style="background: var(--danger-light); color: var(--danger);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        </div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Inventory Occupancy</div>
            <div class="metric-value" style="color: var(--info);"><?= $occupancyRate ?>%</div>
            <div class="metric-meta" style="color: var(--info);"><?= $occupiedUnits ?> of <?= $totalUnits ?> units allocated</div>
        </div>
        <div class="metric-icon-box" style="background: var(--info-light); color: var(--info);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>
</div>

<!-- Master Reports Directory Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
    <!-- Report 1: Sales & Revenue -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Property Sales & Revenue</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">Transactions & Invoicing</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Detailed audit of booked property units, token deposits, sale contracts, and cumulative booking turnover filtered by fiscal year.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/reports/sales" class="btn btn-primary" style="width: 100%; justify-content: center;">Open Sales Report &rsaquo;</a>
        </div>
    </div>

    <!-- Report 2: Rental & Leases -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Rental & Lease Performance</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">Tenancy & Rent Recovery</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Lease maturity tracking, rent collections vs demands, deposit balances, and tenant rent roll across managed properties.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/reports/rental" class="btn btn-primary" style="width: 100%; justify-content: center;">Open Rental Report &rsaquo;</a>
        </div>
    </div>

    <!-- Report 3: Operating Expenses -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Property Expense Audit</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">Opex, Repairs & Taxes</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Monthly operational spending analysis, repair orders, municipal taxes, utility vouchers, and category-level cost breakdown.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/expenses/report" class="btn btn-primary" style="width: 100%; justify-content: center;">Open Expense Report &rsaquo;</a>
        </div>
    </div>

    <!-- Report 4: Marketing Performance -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Marketing & Campaigns</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">CAC, CPL & Lead Conversions</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Campaign budget utilization, cost-per-lead (CPL), conversion velocity, and portal listing ROI calculations.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/campaigns" class="btn btn-primary" style="width: 100%; justify-content: center;">Open Campaign Analytics &rsaquo;</a>
        </div>
    </div>

    <!-- Report 5: GST Statutory Compliance -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">GST Statutory Statements</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">GSTR-1, GSTR-3B & Tax Ledgers</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Monthly output GST liability on sales invoices, commercial rentals, and statutory tax reporting summaries.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/reports/gst" class="btn btn-primary" style="width: 100%; justify-content: center;">Open GST Report &rsaquo;</a>
        </div>
    </div>

    <!-- Report 6: TDS Certificates & Ledger -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">TDS Withholding Ledger</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">Section 194-IA / 194-IB / 194H</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Tax deduction at source registers, broker commission withholdings, property buyer TDS and certificate issuances.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/reports/tds" class="btn btn-primary" style="width: 100%; justify-content: center;">Open TDS Ledger &rsaquo;</a>
        </div>
    </div>

    <!-- Report 7: Receivables Ageing -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 44px; height: 44px; border-radius: var(--border-radius); background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600;">Receivables Ageing Analysis</h3>
                    <span style="font-size: 0.75rem; color: var(--slate-400);">Overdue Buckets (0-30, 31-60, 90+ Days)</span>
                </div>
            </div>
            <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.5; margin-bottom: 1rem;">
                Comprehensive payment ageing schedule classifying outstanding buyer installments and tenant dues by ageing buckets.
            </p>
        </div>
        <div class="card-footer" style="background: var(--slate-50); border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem;">
            <a href="/reports/receivables" class="btn btn-primary" style="width: 100%; justify-content: center;">Open Receivables Ageing &rsaquo;</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
