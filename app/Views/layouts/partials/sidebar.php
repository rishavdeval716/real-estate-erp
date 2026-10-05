<?php
$currentUri = service('uri')->getPath();
$session = session();
$userPermissions = $session->get('permissions') ?? [];
$isSuperAdmin = $session->get('is_super_admin') ?? false;

// Helper to check permission
$can = function($permissionSlug) use ($isSuperAdmin, $userPermissions) {
    if ($isSuperAdmin) return true;
    return in_array($permissionSlug, $userPermissions, true);
};

// All 10 navigation categories with strictly preserved modules, routes, icons, and permissions
$navCategories = [
    [
        'id' => 'overview',
        'title' => 'Overview',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
        'items' => [
            [
                'title' => 'Dashboard',
                'url' => '/dashboard',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
                'active' => (strpos($currentUri, 'dashboard') !== false || $currentUri === '' || $currentUri === '/'),
                'can' => true,
            ],
            [
                'title' => 'Notifications & Alerts',
                'url' => '/notifications',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
                'active' => strpos($currentUri, 'notifications') !== false,
                'can' => $can('notifications.view'),
            ],
        ],
    ],
    [
        'id' => 'property-management',
        'title' => 'Property Management',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'items' => [
            [
                'title' => 'Properties',
                'url' => '/properties',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
                'active' => strpos($currentUri, 'properties') !== false,
                'can' => $can('properties.view'),
            ],
            [
                'title' => 'Property Types',
                'url' => '/property-types',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>',
                'active' => strpos($currentUri, 'property-types') !== false,
                'can' => $can('property_types.view'),
            ],
            [
                'title' => 'Property Units',
                'url' => '/units',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
                'active' => strpos($currentUri, 'units') !== false,
                'can' => $can('units.view'),
            ],
            [
                'title' => 'Projects',
                'url' => '/projects',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>',
                'active' => strpos($currentUri, 'projects') !== false,
                'can' => $can('projects.view'),
            ],
            [
                'title' => 'Locations',
                'url' => '/locations',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
                'active' => strpos($currentUri, 'locations') !== false,
                'can' => $can('locations.view'),
            ],
            [
                'title' => 'Amenities',
                'url' => '/amenities',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'active' => strpos($currentUri, 'amenities') !== false,
                'can' => $can('amenities.view'),
            ],
            [
                'title' => 'Availability',
                'url' => '/availability',
                'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'availability') !== false,
                'can' => $can('availability.view'),
            ],
            [
                'title' => 'Pricing',
                'url' => '/pricing',
                'icon' => '<line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'active' => strpos($currentUri, 'pricing') !== false,
                'can' => $can('pricing.view'),
            ],
            [
                'title' => 'Unit Inventory',
                'url' => '/inventory',
                'icon' => '<path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
                'active' => strpos($currentUri, 'inventory') !== false,
                'can' => $can('inventory.view'),
            ],
            [
                'title' => 'Property Owners',
                'url' => '/owners',
                'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => strpos($currentUri, 'owners') !== false,
                'can' => $can('owners.view'),
            ],
            [
                'title' => 'Property Documents',
                'url' => '/documents',
                'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'active' => strpos($currentUri, 'documents') !== false,
                'can' => $can('documents.view'),
            ],
            [
                'title' => 'Property Verifications',
                'url' => '/verifications',
                'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'verifications') !== false,
                'can' => $can('verifications.view'),
            ],
        ],
    ],
    [
        'id' => 'crm-sales',
        'title' => 'CRM & Sales',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'items' => [
            [
                'title' => 'Leads',
                'url' => '/leads',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => (strpos($currentUri, 'leads') !== false && strpos($currentUri, 'lead-sources') === false),
                'can' => $can('leads.view'),
            ],
            [
                'title' => 'Sales Pipeline',
                'url' => '/pipeline',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>',
                'active' => strpos($currentUri, 'pipeline') !== false,
                'can' => $can('pipeline.view'),
            ],
            [
                'title' => 'Enquiries',
                'url' => '/enquiries',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                'active' => strpos($currentUri, 'enquiries') !== false,
                'can' => $can('enquiries.view'),
            ],
            [
                'title' => 'Follow-ups',
                'url' => '/followups',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'followups') !== false,
                'can' => $can('followups.view'),
            ],
            [
                'title' => 'Site Visits',
                'url' => '/site-visits',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
                'active' => strpos($currentUri, 'site-visits') !== false,
                'can' => $can('site_visits.view'),
            ],
            [
                'title' => 'Unit Holds',
                'url' => '/unit-holds',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
                'active' => strpos($currentUri, 'unit-holds') !== false,
                'can' => $can('unit_holds.view'),
            ],
            [
                'title' => 'Lead Sources',
                'url' => '/lead-sources',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><circle cx="12" cy="12" r="4"/></svg>',
                'active' => strpos($currentUri, 'lead-sources') !== false,
                'can' => $can('lead_sources.view'),
            ],
            [
                'title' => 'Agents & Brokers',
                'url' => '/agents',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>',
                'active' => strpos($currentUri, 'agents') !== false,
                'can' => $can('agents.view'),
            ],
            [
                'title' => 'Marketing Campaigns',
                'url' => '/campaigns',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'campaigns') !== false,
                'can' => $can('campaigns.view'),
            ],
            [
                'title' => 'Customer Comms',
                'url' => '/communications',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                'active' => strpos($currentUri, 'communications') !== false,
                'can' => $can('communications.view'),
            ],
        ],
    ],
    [
        'id' => 'sales-transactions',
        'title' => 'Sales & Transactions',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        'items' => [
            [
                'title' => 'Sales Dashboard',
                'url' => '/sales-dashboard',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
                'active' => strpos($currentUri, 'sales-dashboard') !== false,
                'can' => $can('sales_dashboard.view'),
            ],
            [
                'title' => 'Customers',
                'url' => '/customers',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => strpos($currentUri, 'customers') !== false,
                'can' => $can('customers.view'),
            ],
            [
                'title' => 'Bookings',
                'url' => '/bookings',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><polyline points="10 2 10 10 13 7 16 10 16 2"/></svg>',
                'active' => strpos($currentUri, 'bookings') !== false,
                'can' => $can('bookings.view'),
            ],
            [
                'title' => 'Agreements',
                'url' => '/agreements',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'active' => strpos($currentUri, 'agreements') !== false,
                'can' => $can('agreements.view'),
            ],
            [
                'title' => 'Payments',
                'url' => '/payments',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
                'active' => strpos($currentUri, 'payments') !== false,
                'can' => $can('payments.view'),
            ],
            [
                'title' => 'Invoices',
                'url' => '/invoices',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'active' => strpos($currentUri, 'invoices') !== false,
                'can' => $can('invoices.view'),
            ],
            [
                'title' => 'Receipts',
                'url' => '/receipts',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'receipts') !== false,
                'can' => $can('receipts.view'),
            ],
            [
                'title' => 'Commissions',
                'url' => '/commissions',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>',
                'active' => strpos($currentUri, 'commissions') !== false,
                'can' => $can('commissions.view'),
            ],
            [
                'title' => 'Property Expenses',
                'url' => '/expenses',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
                'active' => strpos($currentUri, 'expenses') !== false,
                'can' => $can('expenses.view'),
            ],
        ],
    ],
    [
        'id' => 'rentals-leasing',
        'title' => 'Rentals & Leasing',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        'items' => [
            [
                'title' => 'Tenants & KYC',
                'url' => '/tenants',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => strpos($currentUri, 'tenants') !== false,
                'can' => $can('tenants.view'),
            ],
            [
                'title' => 'Lease Agreements',
                'url' => '/leases',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                'active' => strpos($currentUri, 'leases') !== false,
                'can' => $can('leases.view'),
            ],
            [
                'title' => 'Security Deposits',
                'url' => '/deposits',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
                'active' => strpos($currentUri, 'deposits') !== false,
                'can' => $can('deposits.view'),
            ],
            [
                'title' => 'Rent Demands',
                'url' => '/rent-demands',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'active' => strpos($currentUri, 'rent-demands') !== false,
                'can' => $can('rent_demands.view'),
            ],
            [
                'title' => 'Rent Collections',
                'url' => '/rent-collections',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'rent-collections') !== false,
                'can' => $can('rent_collections.view'),
            ],
        ],
    ],
    [
        'id' => 'facility-operations',
        'title' => 'Facility & Operations',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'items' => [
            [
                'title' => 'Work Orders',
                'url' => '/maintenance',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
                'active' => (strpos($currentUri, 'maintenance') !== false && strpos($currentUri, 'preventive') === false),
                'can' => $can('maintenance.view'),
            ],
            [
                'title' => 'Resident Complaints',
                'url' => '/complaints',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                'active' => strpos($currentUri, 'complaints') !== false,
                'can' => $can('complaints.view'),
            ],
            [
                'title' => 'Preventive AMC',
                'url' => '/preventive-maintenance',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'preventive-maintenance') !== false,
                'can' => $can('preventive.view'),
            ],
            [
                'title' => 'Facility Assets',
                'url' => '/facility-assets',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 22v-4h6v4"/></svg>',
                'active' => strpos($currentUri, 'facility-assets') !== false,
                'can' => $can('facility_assets.view'),
            ],
            [
                'title' => 'Technicians',
                'url' => '/technicians',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => strpos($currentUri, 'technicians') !== false,
                'can' => $can('technicians.view'),
            ],
            [
                'title' => 'CAM Billing',
                'url' => '/cam-charges',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                'active' => strpos($currentUri, 'cam-charges') !== false,
                'can' => $can('cam.view'),
            ],
        ],
    ],
    [
        'id' => 'construction-handover',
        'title' => 'Construction & Handover',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
        'items' => [
            [
                'title' => 'Site Dashboard',
                'url' => '/construction/dashboard',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
                'active' => strpos($currentUri, 'construction/dashboard') !== false,
                'can' => $can('construction.dashboard.view'),
            ],
            [
                'title' => 'Milestones & Progress',
                'url' => '/construction/milestones',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
                'active' => strpos($currentUri, 'construction/milestones') !== false,
                'can' => $can('construction.milestones.view'),
            ],
            [
                'title' => 'Daily Site Logs',
                'url' => '/construction/daily-logs',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'active' => strpos($currentUri, 'construction/daily-logs') !== false,
                'can' => $can('construction.daily_logs.view'),
            ],
            [
                'title' => 'Contractors & Vendors',
                'url' => '/contractors',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => (strpos($currentUri, 'contractors') !== false && strpos($currentUri, 'work-orders') === false),
                'can' => $can('contractors.view'),
            ],
            [
                'title' => 'Work Contracts',
                'url' => '/construction/work-orders',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
                'active' => strpos($currentUri, 'construction/work-orders') !== false,
                'can' => $can('contractors.work_orders.view'),
            ],
            [
                'title' => 'Material Indents',
                'url' => '/procurement',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
                'active' => strpos($currentUri, 'procurement') !== false,
                'can' => $can('procurement.view'),
            ],
            [
                'title' => 'Quality Inspections',
                'url' => '/inspections',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
                'active' => strpos($currentUri, 'inspections') !== false,
                'can' => $can('inspections.view'),
            ],
            [
                'title' => 'Possession Handover',
                'url' => '/handover',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'handover') !== false,
                'can' => $can('handover.view'),
            ],
        ],
    ],
    [
        'id' => 'portals',
        'title' => 'Self-Service Portals',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
        'items' => [
            [
                'title' => 'Owner Portal',
                'url' => '/portal/owner',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
                'active' => strpos($currentUri, 'portal/owner') !== false,
                'can' => $can('portal.owner'),
            ],
            [
                'title' => 'Tenant Portal',
                'url' => '/portal/tenant',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                'active' => strpos($currentUri, 'portal/tenant') !== false,
                'can' => $can('portal.tenant'),
            ],
            [
                'title' => 'Partner Portal',
                'url' => '/portal/partner',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>',
                'active' => strpos($currentUri, 'portal/partner') !== false,
                'can' => $can('portal.partner'),
            ],
        ],
    ],
    [
        'id' => 'compliance-reports',
        'title' => 'Compliance & Reports',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        'items' => [
            [
                'title' => 'Reports Hub',
                'url' => '/reports',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
                'active' => ($currentUri === 'reports' || $currentUri === 'reports/'),
                'can' => $can('reports.view'),
            ],
            [
                'title' => 'GST Reports',
                'url' => '/reports/gst',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
                'active' => strpos($currentUri, 'reports/gst') !== false,
                'can' => $can('reports.gst'),
            ],
            [
                'title' => 'TDS & Form 16A',
                'url' => '/reports/tds',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'reports/tds') !== false,
                'can' => $can('reports.tds'),
            ],
            [
                'title' => 'Receivables Ageing',
                'url' => '/reports/receivables',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>',
                'active' => strpos($currentUri, 'reports/receivables') !== false,
                'can' => $can('reports.receivables'),
            ],
        ],
    ],
    [
        'id' => 'administration',
        'title' => 'Administration',
        'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
        'items' => [
            [
                'title' => 'Users',
                'url' => '/users',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'active' => strpos($currentUri, 'users') !== false,
                'can' => $can('users.view'),
            ],
            [
                'title' => 'Roles',
                'url' => '/roles',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'roles') !== false,
                'can' => $can('roles.view'),
            ],
            [
                'title' => 'Permissions',
                'url' => '/permissions',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>',
                'active' => strpos($currentUri, 'permissions') !== false,
                'can' => $can('permissions.view'),
            ],
            [
                'title' => 'Company',
                'url' => '/company',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M9 12h1"/><path d="M9 16h1"/><path d="M14 8h1"/><path d="M14 12h1"/><path d="M14 16h1"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/></svg>',
                'active' => strpos($currentUri, 'company') !== false,
                'can' => $can('company.view'),
            ],
            [
                'title' => 'Branches',
                'url' => '/branches',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
                'active' => strpos($currentUri, 'branches') !== false,
                'can' => $can('branches.view'),
            ],
            [
                'title' => 'Audit Logs',
                'url' => '/audit-logs',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                'active' => strpos($currentUri, 'audit-logs') !== false,
                'can' => $can('audit_logs.view'),
            ],
            [
                'title' => 'System Settings',
                'url' => '/settings',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
                'active' => strpos($currentUri, 'settings') !== false,
                'can' => $can('settings.view'),
            ],
        ],
    ],
];
?>
<aside class="app-sidebar">
    <div class="sidebar-header">
        <div class="brand-logo-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                <path d="M9 22v-4h6v4"></path>
                <path d="M8 6h.01"></path>
                <path d="M16 6h.01"></path>
                <path d="M8 10h.01"></path>
                <path d="M16 10h.01"></path>
                <path d="M8 14h.01"></path>
                <path d="M16 14h.01"></path>
            </svg>
        </div>
        <div class="brand-text">
            <span class="brand-title">Real Estate ERP</span>
            <span class="brand-subtitle">Enterprise Suite</span>
        </div>
        <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Live Module Search Filter -->
    <div class="sidebar-search-container">
        <div class="sidebar-search-box">
            <svg class="sidebar-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="sidebarSearchInput" class="sidebar-search-input" placeholder="Search modules..." autocomplete="off" spellcheck="false" aria-label="Search navigation modules" />
            <button type="button" id="sidebarSearchClear" class="sidebar-search-clear" aria-label="Clear search" title="Clear search">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Collapsible Navigation Accordion -->
    <nav class="sidebar-menu" id="sidebarMenuNav" aria-label="Main Navigation">
        <?php foreach ($navCategories as $cat): ?>
            <?php
            // Filter permitted items
            $permittedItems = array_filter($cat['items'], function($it) {
                return !empty($it['can']);
            });
            if (empty($permittedItems)) continue;

            // Check if any child item in this category is currently active
            $hasActiveChild = false;
            foreach ($permittedItems as $it) {
                if (!empty($it['active'])) {
                    $hasActiveChild = true;
                    break;
                }
            }
            $isOpen = $hasActiveChild;
            ?>
            <div data-category="<?= $cat['id'] ?>" class="nav-category <?= $isOpen ? 'open active-category' : '' ?>">
                <button type="button" 
                        class="category-header" 
                        id="btn-cat-<?= $cat['id'] ?>"
                        aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" 
                        aria-controls="submenu-<?= $cat['id'] ?>"
                        title="<?= esc($cat['title']) ?>">
                    <span class="category-icon" aria-hidden="true">
                        <?= $cat['icon'] ?>
                    </span>
                    <span class="category-title"><?= esc($cat['title']) ?></span>
                    <span class="category-badge" title="<?= count($permittedItems) ?> modules"><?= count($permittedItems) ?></span>
                    <svg class="category-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <ul class="category-submenu" id="submenu-<?= $cat['id'] ?>" role="region" aria-labelledby="btn-cat-<?= $cat['id'] ?>">
                    <?php foreach ($permittedItems as $item): ?>
                        <li class="menu-item">
                            <a href="<?= $item['url'] ?>" class="menu-link <?= !empty($item['active']) ? 'active' : '' ?>" title="<?= esc($item['title']) ?>">
                                <span class="menu-icon" aria-hidden="true">
                                    <?= $item['icon'] ?>
                                </span>
                                <span><?= esc($item['title']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <div id="sidebarNoResults" class="sidebar-no-results" style="display: none;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.5rem; opacity: 0.6;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <div>No matching modules found</div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-avatar-sm">
            <?= strtoupper(substr(esc($session->get('user_name') ?? 'A'), 0, 1)) ?>
        </div>
        <div class="user-info-text">
            <div class="user-name-text"><?= esc($session->get('user_name') ?? 'Administrator') ?></div>
            <div class="user-role-badge"><?= esc($session->get('role_name') ?? 'Super Admin') ?></div>
        </div>
        <a href="/logout" title="Sign Out" style="color: var(--slate-400); display: flex;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
        </a>
    </div>
</aside>
