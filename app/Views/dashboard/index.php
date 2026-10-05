<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <span>Administration</span>
            <span class="separator">/</span>
            <span>Dashboard</span>
        </div>
        <h1 class="page-title">Executive Dashboard</h1>
        <p class="page-subtitle">Real Estate ERP Phase 1 &bull; System Foundation Overview</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <?php if (session()->get('is_super_admin') || in_array('users.create', session()->get('permissions') ?? [])): ?>
            <a href="/users/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                New User
            </a>
        <?php endif; ?>
        <?php if (session()->get('is_super_admin') || in_array('branches.create', session()->get('permissions') ?? [])): ?>
            <a href="/branches/create" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><line x1="12" x2="12" y1="11" y2="17"/><line x1="9" x2="15" y1="14" y2="14"/></svg>
                New Branch
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Foundation Metrics Grid -->
<div class="metric-grid foundation-grid">
    <!-- Total Users -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Users</div>
            <div class="metric-value"><?= number_format($totalUsers) ?></div>
            <div class="metric-meta">
                <span>All registered accounts</span>
            </div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

    <!-- Active Users -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Active Users</div>
            <div class="metric-value"><?= number_format($activeUsers) ?></div>
            <div class="metric-meta" style="color: var(--success);">
                <span><?= $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 ?>% enabled status</span>
            </div>
        </div>
        <div class="metric-icon-box success">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>

    <!-- Total Roles -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Configured Roles</div>
            <div class="metric-value"><?= number_format($totalRoles) ?></div>
            <div class="metric-meta">
                <span>Access security tiers</span>
            </div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>

    <!-- Total Branches -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Total Branches</div>
            <div class="metric-value"><?= number_format($totalBranches) ?></div>
            <div class="metric-meta">
                <span>Active regional locations</span>
            </div>
        </div>
        <div class="metric-icon-box info">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>
</div>

<!-- Phase 2 Property & Inventory Foundation Metrics Grid -->
<div style="margin: 1.5rem 0 0.5rem 0;">
    <h3 style="font-size: 1rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">Property & Inventory Overview</h3>
    <p style="font-size: 0.8rem; color: var(--slate-500); margin: 0;">Live inventory metrics calculated dynamically from database</p>
</div>

<div class="metric-grid property-grid">
    <!-- Total Properties -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Properties</div>
            <div class="metric-value"><?= number_format($totalProperties) ?></div>
            <div class="metric-meta"><a href="/properties" style="color: var(--primary); text-decoration: none; font-weight: 600;">View listings &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
    </div>

    <!-- Available Properties -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Available Properties</div>
            <div class="metric-value"><?= number_format($availableProperties) ?></div>
            <div class="metric-meta" style="color: var(--success);"><span>Ready for deal</span></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>

    <!-- Reserved Properties -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Reserved Properties</div>
            <div class="metric-value"><?= number_format($reservedProperties) ?></div>
            <div class="metric-meta"><span>Client holds</span></div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>

    <!-- Booked Properties -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Booked Properties</div>
            <div class="metric-value"><?= number_format($bookedProperties) ?></div>
            <div class="metric-meta"><span>Deposit received</span></div>
        </div>
        <div class="metric-icon-box info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
    </div>

    <!-- Sold Properties -->
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Sold Properties</div>
            <div class="metric-value"><?= number_format($soldProperties) ?></div>
            <div class="metric-meta"><span>Concluded sales</span></div>
        </div>
        <div class="metric-icon-box secondary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
    </div>

    <!-- Rented Properties -->
    <div class="metric-card" style="border-left: 4px solid var(--slate-700);">
        <div>
            <div class="metric-label">Rented Properties</div>
            <div class="metric-value"><?= number_format($rentedProperties) ?></div>
            <div class="metric-meta"><span>Active tenancies</span></div>
        </div>
        <div class="metric-icon-box" style="background: var(--slate-100); color: var(--slate-700);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 21v-8a3 3 0 0 0-6 0v8"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
        </div>
    </div>

    <!-- Total Projects -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Total Projects</div>
            <div class="metric-value"><?= number_format($totalProjects) ?></div>
            <div class="metric-meta"><a href="/projects" style="color: var(--info); text-decoration: none; font-weight: 600;">Developments &rarr;</a></div>
        </div>
        <div class="metric-icon-box info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/></svg>
        </div>
    </div>

    <!-- Total Units -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Units</div>
            <div class="metric-value"><?= number_format($totalUnits) ?></div>
            <div class="metric-meta"><a href="/units" style="color: var(--primary); text-decoration: none; font-weight: 600;">Inventory units &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Z"/></svg>
        </div>
    </div>

    <!-- Available Units -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Available Units</div>
            <div class="metric-value"><?= number_format($availableUnits) ?></div>
            <div class="metric-meta" style="color: var(--success);"><a href="/inventory" style="color: var(--success); text-decoration: none; font-weight: 600;">Inventory Matrix &rarr;</a></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
        </div>
    </div>
</div>

<!-- Phase 3 CRM & Sales Pipeline Metrics Grid -->
<div style="margin: 1.5rem 0 0.5rem 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">CRM & Sales Pipeline Overview</h3>
        <p style="font-size: 0.8rem; color: var(--slate-500); margin: 0;">Real-time lead conversion, enquiries, follow-ups, and unit reservations</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/pipeline" class="btn btn-sm btn-primary">Pipeline Board</a>
        <a href="/leads/create" class="btn btn-sm btn-secondary">+ New Lead</a>
    </div>
</div>

<div class="metric-grid crm-grid">
    <!-- Total Leads -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Leads</div>
            <div class="metric-value"><?= number_format($totalLeads) ?></div>
            <div class="metric-meta"><a href="/leads" style="color: var(--primary); text-decoration: none; font-weight: 600;">View leads &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
    </div>

    <!-- New Leads -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">New Leads</div>
            <div class="metric-value"><?= number_format($newLeads) ?></div>
            <div class="metric-meta"><a href="/leads?lead_status=New" style="color: var(--info); text-decoration: none; font-weight: 600;">Uncontacted &rarr;</a></div>
        </div>
        <div class="metric-icon-box info">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
    </div>

    <!-- Qualified Leads -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Qualified Leads</div>
            <div class="metric-value"><?= number_format($qualifiedLeads) ?></div>
            <div class="metric-meta" style="color: var(--success);"><a href="/leads?lead_status=Qualified" style="color: var(--success); text-decoration: none; font-weight: 600;">Verified budget &rarr;</a></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>

    <!-- Open Enquiries -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Open Enquiries</div>
            <div class="metric-value"><?= number_format($openEnquiries) ?></div>
            <div class="metric-meta"><a href="/enquiries" style="color: var(--warning); text-decoration: none; font-weight: 600;">Pending review &rarr;</a></div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
    </div>

    <!-- Today's Follow-ups -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Today's Follow-ups</div>
            <div class="metric-value"><?= number_format($todayFollowups) ?></div>
            <div class="metric-meta"><a href="/followups?tab=today" style="color: var(--primary); text-decoration: none; font-weight: 600;">Due today &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
    </div>

    <!-- Overdue Follow-ups -->
    <div class="metric-card" style="border-left: 4px solid var(--danger);">
        <div>
            <div class="metric-label">Overdue Follow-ups</div>
            <div class="metric-value" style="color: var(--danger);"><?= number_format($overdueFollowups) ?></div>
            <div class="metric-meta"><a href="/followups?tab=overdue" style="color: var(--danger); text-decoration: none; font-weight: 600;">Immediate action &rarr;</a></div>
        </div>
        <div class="metric-icon-box" style="background: #fef2f2; color: #b91c1c;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
    </div>

    <!-- Today's Site Visits -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Today's Visits</div>
            <div class="metric-value"><?= number_format($todayVisits) ?></div>
            <div class="metric-meta"><a href="/site-visits" style="color: var(--info); text-decoration: none; font-weight: 600;">Tours scheduled &rarr;</a></div>
        </div>
        <div class="metric-icon-box info">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
    </div>

    <!-- Upcoming Site Visits -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Upcoming Visits</div>
            <div class="metric-value"><?= number_format($upcomingVisits) ?></div>
            <div class="metric-meta"><a href="/site-visits?status=Scheduled" style="color: var(--primary); text-decoration: none; font-weight: 600;">Future bookings &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
    </div>

    <!-- Active Unit Holds -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Active Unit Holds</div>
            <div class="metric-value"><?= number_format($activeUnitHolds) ?></div>
            <div class="metric-meta"><a href="/unit-holds?status=Active" style="color: var(--warning); text-decoration: none; font-weight: 600;">Token holds &rarr;</a></div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
    </div>

    <!-- Leads Won -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Leads Won</div>
            <div class="metric-value"><?= number_format($leadsWon) ?></div>
            <div class="metric-meta" style="color: var(--success);"><a href="/pipeline" style="color: var(--success); text-decoration: none; font-weight: 600;">Deals closed &rarr;</a></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-2.34"/><path d="M14 14.66V17c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-2.34"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
        </div>
    </div>

    <!-- Leads Lost -->
    <div class="metric-card secondary">
        <div>
            <div class="metric-label">Leads Lost</div>
            <div class="metric-value"><?= number_format($leadsLost) ?></div>
            <div class="metric-meta"><a href="/leads?lead_status=Lost" style="color: var(--slate-500); text-decoration: none; font-weight: 600;">Lost reasons &rarr;</a></div>
        </div>
        <div class="metric-icon-box secondary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
    </div>
</div>

<!-- Phase 4 Real Estate Sales & Financial Transaction Performance Grid -->
<div style="margin: 1.5rem 0 0.5rem 0; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">Sales & Financial Transactions Performance</h3>
        <p style="font-size: 0.8rem; color: var(--slate-500); margin: 0;">Live revenue realization, milestone collections, and active transaction metrics from MySQL</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/sales-dashboard" class="btn btn-sm btn-primary">Sales Analytics</a>
        <a href="/bookings/create" class="btn btn-sm btn-secondary">+ New Booking</a>
    </div>
</div>

<div class="metric-grid financial-grid">
    <!-- Total Booking Value -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Total Booking Value</div>
            <div class="metric-value">₹<?= number_format($salesSummary['total_booking_value'] ?? 0, 2) ?></div>
            <div class="metric-meta"><a href="/bookings" style="color: var(--primary); text-decoration: none; font-weight: 600;"><?= $salesSummary['confirmed_bookings'] ?? 0 ?> confirmed sales &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
    </div>

    <!-- Total Collected -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Total Collected</div>
            <div class="metric-value">₹<?= number_format($salesSummary['total_collected'] ?? 0, 2) ?></div>
            <div class="metric-meta" style="color: var(--success);"><a href="/payments" style="color: var(--success); text-decoration: none; font-weight: 600;">Verified payments &rarr;</a></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
    </div>

    <!-- Total Outstanding -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Total Outstanding</div>
            <div class="metric-value">₹<?= number_format($salesSummary['total_outstanding'] ?? 0, 2) ?></div>
            <div class="metric-meta"><a href="/bookings" style="color: var(--warning); text-decoration: none; font-weight: 600;">Receivables due &rarr;</a></div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        </div>
    </div>

    <!-- Overdue Amount -->
    <div class="metric-card danger">
        <div>
            <div class="metric-label">Overdue Amount</div>
            <div class="metric-value">₹<?= number_format($salesSummary['overdue_amount'] ?? 0, 2) ?></div>
            <div class="metric-meta" style="color: var(--danger);"><span>Milestones past due</span></div>
        </div>
        <div class="metric-icon-box danger">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
    </div>
</div>

<!-- Operations & Compliance Overview -->
<div style="margin: 1.5rem 0 0.5rem 0;">
    <h3 style="font-size: 1rem; font-weight: 700; color: var(--slate-800); margin-bottom: 0.25rem;">Enterprise Operations & Compliance Overview</h3>
    <p style="font-size: 0.8rem; color: var(--slate-500); margin: 0;">Operational status covering construction execution, tenancy management, facilities, and statutory audits</p>
</div>

<div class="metric-grid foundation-grid" style="margin-bottom: 2rem;">
    <!-- Construction Execution -->
    <div class="metric-card primary">
        <div>
            <div class="metric-label">Construction Execution</div>
            <div class="metric-value"><?= number_format($totalProjects) ?></div>
            <div class="metric-meta"><a href="/construction/dashboard" style="color: var(--primary); text-decoration: none; font-weight: 600;">Site Milestones &rarr;</a></div>
        </div>
        <div class="metric-icon-box primary">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
    </div>

    <!-- Rental & Tenancies -->
    <div class="metric-card info">
        <div>
            <div class="metric-label">Rental & Tenancies</div>
            <div class="metric-value"><?= number_format($rentedProperties) ?></div>
            <div class="metric-meta"><a href="/leases" style="color: var(--info); text-decoration: none; font-weight: 600;">Active Leases &rarr;</a></div>
        </div>
        <div class="metric-icon-box info">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 21v-8a3 3 0 0 0-6 0v8"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
        </div>
    </div>

    <!-- Facilities & Work Orders -->
    <div class="metric-card warning">
        <div>
            <div class="metric-label">Facilities & Work Orders</div>
            <div class="metric-value">Active</div>
            <div class="metric-meta"><a href="/maintenance" style="color: var(--warning); text-decoration: none; font-weight: 600;">Maintenance &rarr;</a></div>
        </div>
        <div class="metric-icon-box warning">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
    </div>

    <!-- Compliance & Reports -->
    <div class="metric-card success">
        <div>
            <div class="metric-label">Compliance & Reports</div>
            <div class="metric-value">Verified</div>
            <div class="metric-meta"><a href="/reports" style="color: var(--success); text-decoration: none; font-weight: 600;">Reports Hub &rarr;</a></div>
        </div>
        <div class="metric-icon-box success">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
    </div>
</div>

<div class="dashboard-tables-grid">
    <!-- Recent Logins -->
    <div>
        <div class="card" style="margin-bottom: 0; height: 100%;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Recent User Logins</h2>
                    <div class="card-subtitle">Latest active sessions across branches</div>
                </div>
                <a href="/users" class="btn btn-sm btn-secondary">View Users</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Last Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentLogins)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 2rem;">No recent logins recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentLogins as $loginUser): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; color: var(--slate-900);"><?= esc($loginUser['name']) ?></div>
                                            <div style="font-size: 0.76rem; color: var(--slate-500);"><?= esc($loginUser['email']) ?></div>
                                        </td>
                                        <td><?= esc($loginUser['branch_name'] ?? 'Corporate HQ') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $loginUser['status'] ?>">
                                                <span class="badge-dot"></span>
                                                <?= ucfirst(esc($loginUser['status'])) ?>
                                            </span>
                                        </td>
                                        <td style="font-size: 0.8rem; color: var(--slate-600); white-space: nowrap;">
                                            <?= esc(date('M d, Y h:i A', strtotime($loginUser['last_login_at']))) ?>
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

    <!-- Recent Audit Activities -->
    <div>
        <div class="card" style="margin-bottom: 0; height: 100%;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Recent Audit Activities</h2>
                    <div class="card-subtitle">Real-time system transaction events</div>
                </div>
                <a href="/audit-logs" class="btn btn-sm btn-secondary">Audit Trail</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Actor</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentActivities)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--slate-400); padding: 2rem;">No system activities logged yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentActivities as $act): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-role" style="background: var(--slate-100); color: var(--slate-700); border-color: var(--slate-200);">
                                                <?= esc($act['action']) ?>
                                            </span>
                                        </td>
                                        <td style="font-weight: 600; color: var(--slate-800);"><?= esc($act['module']) ?></td>
                                        <td><?= esc($act['user_name'] ?? 'System') ?></td>
                                        <td style="font-size: 0.8rem; color: var(--slate-600); white-space: nowrap;">
                                            <?= esc(date('M d, Y h:i A', strtotime($act['created_at']))) ?>
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
