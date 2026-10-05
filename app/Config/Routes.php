<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Root URL Routing
$routes->get('/', function() {
    if (session()->get('is_logged_in')) {
        return redirect()->to('/dashboard');
    }
    return redirect()->to('/login');
});

// Authentication Routes (Public)
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::attemptLogin');
$routes->match(['get', 'post'], '/logout', 'AuthController::logout');

// Protected Routes (Requires Authentication Filter)
$routes->group('', ['filter' => 'auth'], function($routes) {

    // Dashboard
    $routes->get('/dashboard', 'DashboardController::index', ['filter' => 'permission:dashboard.view']);

    // User Management
    $routes->group('users', function($routes) {
        $routes->get('/', 'UserController::index', ['filter' => 'permission:users.view']);
        $routes->get('create', 'UserController::create', ['filter' => 'permission:users.create']);
        $routes->post('store', 'UserController::store', ['filter' => 'permission:users.create']);
        $routes->get('view/(:num)', 'UserController::show/$1', ['filter' => 'permission:users.view']);
        $routes->get('edit/(:num)', 'UserController::edit/$1', ['filter' => 'permission:users.edit']);
        $routes->post('update/(:num)', 'UserController::update/$1', ['filter' => 'permission:users.edit']);
        $routes->post('delete/(:num)', 'UserController::delete/$1', ['filter' => 'permission:users.delete']);
    });

    // Role Management
    $routes->group('roles', function($routes) {
        $routes->get('/', 'RoleController::index', ['filter' => 'permission:roles.view']);
        $routes->get('create', 'RoleController::create', ['filter' => 'permission:roles.create']);
        $routes->post('store', 'RoleController::store', ['filter' => 'permission:roles.create']);
        $routes->get('view/(:num)', 'RoleController::show/$1', ['filter' => 'permission:roles.view']);
        $routes->get('edit/(:num)', 'RoleController::edit/$1', ['filter' => 'permission:roles.edit']);
        $routes->post('update/(:num)', 'RoleController::update/$1', ['filter' => 'permission:roles.edit']);
        $routes->post('delete/(:num)', 'RoleController::delete/$1', ['filter' => 'permission:roles.delete']);
    });

    // Permission Management
    $routes->group('permissions', function($routes) {
        $routes->get('/', 'PermissionController::index', ['filter' => 'permission:permissions.view']);
        $routes->get('create', 'PermissionController::create', ['filter' => 'permission:permissions.create']);
        $routes->post('store', 'PermissionController::store', ['filter' => 'permission:permissions.create']);
        $routes->get('edit/(:num)', 'PermissionController::edit/$1', ['filter' => 'permission:permissions.edit']);
        $routes->post('update/(:num)', 'PermissionController::update/$1', ['filter' => 'permission:permissions.edit']);
        $routes->post('delete/(:num)', 'PermissionController::delete/$1', ['filter' => 'permission:permissions.delete']);
    });

    // Company Management
    $routes->group('company', function($routes) {
        $routes->get('/', 'CompanyController::index', ['filter' => 'permission:company.view']);
        $routes->get('edit', 'CompanyController::edit', ['filter' => 'permission:company.edit']);
        $routes->post('update', 'CompanyController::update', ['filter' => 'permission:company.edit']);
    });

    // Branch Management
    $routes->group('branches', function($routes) {
        $routes->get('/', 'BranchController::index', ['filter' => 'permission:branches.view']);
        $routes->get('create', 'BranchController::create', ['filter' => 'permission:branches.create']);
        $routes->post('store', 'BranchController::store', ['filter' => 'permission:branches.create']);
        $routes->get('view/(:num)', 'BranchController::show/$1', ['filter' => 'permission:branches.view']);
        $routes->get('edit/(:num)', 'BranchController::edit/$1', ['filter' => 'permission:branches.edit']);
        $routes->post('update/(:num)', 'BranchController::update/$1', ['filter' => 'permission:branches.edit']);
        $routes->post('delete/(:num)', 'BranchController::delete/$1', ['filter' => 'permission:branches.delete']);
    });

    // Audit Logs
    $routes->get('/audit-logs', 'AuditLogController::index', ['filter' => 'permission:audit_logs.view']);

    // ==========================================
    // PHASE 2: PROPERTY MANAGEMENT FOUNDATION
    // ==========================================

    // Property Types
    $routes->group('property-types', function($routes) {
        $routes->get('/', 'PropertyTypeController::index', ['filter' => 'permission:property_types.view']);
        $routes->get('create', 'PropertyTypeController::create', ['filter' => 'permission:property_types.create']);
        $routes->post('store', 'PropertyTypeController::store', ['filter' => 'permission:property_types.create']);
        $routes->get('view/(:num)', 'PropertyTypeController::show/$1', ['filter' => 'permission:property_types.view']);
        $routes->get('edit/(:num)', 'PropertyTypeController::edit/$1', ['filter' => 'permission:property_types.edit']);
        $routes->post('update/(:num)', 'PropertyTypeController::update/$1', ['filter' => 'permission:property_types.edit']);
        $routes->post('delete/(:num)', 'PropertyTypeController::delete/$1', ['filter' => 'permission:property_types.delete']);
    });

    // Locations
    $routes->group('locations', function($routes) {
        $routes->get('/', 'LocationController::index', ['filter' => 'permission:locations.view']);
        $routes->get('create', 'LocationController::create', ['filter' => 'permission:locations.create']);
        $routes->post('store', 'LocationController::store', ['filter' => 'permission:locations.create']);
        $routes->get('view/(:num)', 'LocationController::show/$1', ['filter' => 'permission:locations.view']);
        $routes->get('edit/(:num)', 'LocationController::edit/$1', ['filter' => 'permission:locations.edit']);
        $routes->post('update/(:num)', 'LocationController::update/$1', ['filter' => 'permission:locations.edit']);
        $routes->post('delete/(:num)', 'LocationController::delete/$1', ['filter' => 'permission:locations.delete']);
    });

    // Amenities
    $routes->group('amenities', function($routes) {
        $routes->get('/', 'AmenityController::index', ['filter' => 'permission:amenities.view']);
        $routes->get('create', 'AmenityController::create', ['filter' => 'permission:amenities.create']);
        $routes->post('store', 'AmenityController::store', ['filter' => 'permission:amenities.create']);
        $routes->get('edit/(:num)', 'AmenityController::edit/$1', ['filter' => 'permission:amenities.edit']);
        $routes->post('update/(:num)', 'AmenityController::update/$1', ['filter' => 'permission:amenities.edit']);
        $routes->post('delete/(:num)', 'AmenityController::delete/$1', ['filter' => 'permission:amenities.delete']);
    });

    // Projects & Towers
    $routes->group('projects', function($routes) {
        $routes->get('/', 'ProjectController::index', ['filter' => 'permission:projects.view']);
        $routes->get('create', 'ProjectController::create', ['filter' => 'permission:projects.create']);
        $routes->post('store', 'ProjectController::store', ['filter' => 'permission:projects.create']);
        $routes->get('view/(:num)', 'ProjectController::show/$1', ['filter' => 'permission:projects.view']);
        $routes->get('edit/(:num)', 'ProjectController::edit/$1', ['filter' => 'permission:projects.edit']);
        $routes->post('update/(:num)', 'ProjectController::update/$1', ['filter' => 'permission:projects.edit']);
        $routes->post('delete/(:num)', 'ProjectController::delete/$1', ['filter' => 'permission:projects.delete']);
        $routes->post('towers/store/(:num)', 'ProjectController::addTower/$1', ['filter' => 'permission:projects.edit']);
        $routes->post('towers/delete/(:num)', 'ProjectController::deleteTower/$1', ['filter' => 'permission:projects.edit']);
    });

    // Properties
    $routes->group('properties', function($routes) {
        $routes->get('/', 'PropertyController::index', ['filter' => 'permission:properties.view']);
        $routes->get('create', 'PropertyController::create', ['filter' => 'permission:properties.create']);
        $routes->post('store', 'PropertyController::store', ['filter' => 'permission:properties.create']);
        $routes->get('view/(:num)', 'PropertyController::show/$1', ['filter' => 'permission:properties.view']);
        $routes->get('edit/(:num)', 'PropertyController::edit/$1', ['filter' => 'permission:properties.edit']);
        $routes->post('update/(:num)', 'PropertyController::update/$1', ['filter' => 'permission:properties.edit']);
        $routes->post('delete/(:num)', 'PropertyController::delete/$1', ['filter' => 'permission:properties.delete']);
    });

    // Property Units
    $routes->group('units', function($routes) {
        $routes->get('/', 'PropertyUnitController::index', ['filter' => 'permission:units.view']);
        $routes->get('create', 'PropertyUnitController::create', ['filter' => 'permission:units.create']);
        $routes->post('store', 'PropertyUnitController::store', ['filter' => 'permission:units.create']);
        $routes->get('view/(:num)', 'PropertyUnitController::show/$1', ['filter' => 'permission:units.view']);
        $routes->get('edit/(:num)', 'PropertyUnitController::edit/$1', ['filter' => 'permission:units.edit']);
        $routes->post('update/(:num)', 'PropertyUnitController::update/$1', ['filter' => 'permission:units.edit']);
        $routes->post('delete/(:num)', 'PropertyUnitController::delete/$1', ['filter' => 'permission:units.delete']);
    });

    // Property Media
    $routes->group('media', function($routes) {
        $routes->post('upload', 'PropertyMediaController::upload', ['filter' => 'permission:media.create']);
        $routes->post('primary/(:num)', 'PropertyMediaController::setPrimary/$1', ['filter' => 'permission:media.create']);
        $routes->post('delete/(:num)', 'PropertyMediaController::delete/$1', ['filter' => 'permission:media.delete']);
        $routes->get('file/(:num)', 'PropertyMediaController::viewFile/$1', ['filter' => 'permission:media.view']);
    });

    // Availability Management
    $routes->group('availability', function($routes) {
        $routes->get('/', 'PropertyAvailabilityController::index', ['filter' => 'permission:availability.view']);
        $routes->post('property', 'PropertyAvailabilityController::updatePropertyStatus', ['filter' => 'permission:availability.edit']);
        $routes->post('unit', 'PropertyAvailabilityController::updateUnitStatus', ['filter' => 'permission:availability.edit']);
    });

    // Pricing & Valuation
    $routes->group('pricing', function($routes) {
        $routes->get('/', 'PropertyPricingController::index', ['filter' => 'permission:pricing.view']);
        $routes->get('create', 'PropertyPricingController::create', ['filter' => 'permission:pricing.create']);
        $routes->post('store', 'PropertyPricingController::store', ['filter' => 'permission:pricing.create']);
        $routes->post('delete/(:num)', 'PropertyPricingController::delete/$1', ['filter' => 'permission:pricing.delete']);
    });

    // Unit Inventory Matrix
    $routes->get('/inventory', 'InventoryController::index', ['filter' => 'permission:inventory.view']);

    // ==========================================
    // PHASE 3 — CRM, LEADS & SALES PIPELINE
    // ==========================================

    // Lead Sources
    $routes->group('lead-sources', function($routes) {
        $routes->get('/', 'LeadSourceController::index', ['filter' => 'permission:lead_sources.view']);
        $routes->get('create', 'LeadSourceController::create', ['filter' => 'permission:lead_sources.create']);
        $routes->post('store', 'LeadSourceController::store', ['filter' => 'permission:lead_sources.create']);
        $routes->get('edit/(:num)', 'LeadSourceController::edit/$1', ['filter' => 'permission:lead_sources.edit']);
        $routes->post('update/(:num)', 'LeadSourceController::update/$1', ['filter' => 'permission:lead_sources.edit']);
        $routes->post('delete/(:num)', 'LeadSourceController::delete/$1', ['filter' => 'permission:lead_sources.delete']);
    });

    // Leads
    $routes->group('leads', function($routes) {
        $routes->get('/', 'LeadController::index', ['filter' => 'permission:leads.view']);
        $routes->get('create', 'LeadController::create', ['filter' => 'permission:leads.create']);
        $routes->post('store', 'LeadController::store', ['filter' => 'permission:leads.create']);
        $routes->get('view/(:num)', 'LeadController::view/$1', ['filter' => 'permission:leads.view']);
        $routes->get('edit/(:num)', 'LeadController::edit/$1', ['filter' => 'permission:leads.edit']);
        $routes->post('update/(:num)', 'LeadController::update/$1', ['filter' => 'permission:leads.edit']);
        $routes->post('assign/(:num)', 'LeadController::assign/$1', ['filter' => 'permission:leads.assign']);
        $routes->post('assign-round-robin/(:num)', 'LeadController::assignRoundRobin/$1', ['filter' => 'permission:leads.assign']);
        $routes->post('qualify/(:num)', 'LeadController::qualify/$1', ['filter' => 'permission:leads.change_stage']);
        $routes->post('stage/(:num)', 'LeadController::changeStage/$1', ['filter' => 'permission:leads.change_stage']);
        $routes->post('interest/(:num)', 'LeadController::addInterest/$1', ['filter' => 'permission:leads.edit']);
        $routes->post('delete/(:num)', 'LeadController::delete/$1', ['filter' => 'permission:leads.delete']);
    });

    // Visual CRM Pipeline (Kanban)
    $routes->group('pipeline', function($routes) {
        $routes->get('/', 'PipelineController::index', ['filter' => 'permission:pipeline.view']);
        $routes->post('stage', 'PipelineController::updateStage', ['filter' => 'permission:pipeline.manage']);
    });

    // Enquiries
    $routes->group('enquiries', function($routes) {
        $routes->get('/', 'EnquiryController::index', ['filter' => 'permission:enquiries.view']);
        $routes->post('store', 'EnquiryController::store', ['filter' => 'permission:enquiries.create']);
        $routes->post('status/(:num)', 'EnquiryController::updateStatus/$1', ['filter' => 'permission:enquiries.edit']);
        $routes->post('delete/(:num)', 'EnquiryController::delete/$1', ['filter' => 'permission:enquiries.delete']);
    });

    // Follow-ups
    $routes->group('followups', function($routes) {
        $routes->get('/', 'FollowupController::index', ['filter' => 'permission:followups.view']);
        $routes->post('store', 'FollowupController::store', ['filter' => 'permission:followups.create']);
        $routes->post('complete/(:num)', 'FollowupController::complete/$1', ['filter' => 'permission:followups.complete']);
        $routes->post('reschedule/(:num)', 'FollowupController::reschedule/$1', ['filter' => 'permission:followups.edit']);
        $routes->post('cancel/(:num)', 'FollowupController::cancel/$1', ['filter' => 'permission:followups.edit']);
    });

    // Site Visits
    $routes->group('site-visits', function($routes) {
        $routes->get('/', 'SiteVisitController::index', ['filter' => 'permission:site_visits.view']);
        $routes->post('store', 'SiteVisitController::store', ['filter' => 'permission:site_visits.create']);
        $routes->post('complete/(:num)', 'SiteVisitController::complete/$1', ['filter' => 'permission:site_visits.complete']);
        $routes->post('reschedule/(:num)', 'SiteVisitController::reschedule/$1', ['filter' => 'permission:site_visits.edit']);
        $routes->post('cancel/(:num)', 'SiteVisitController::cancel/$1', ['filter' => 'permission:site_visits.delete']);
    });

    // Unit Holds (Temporary Reservation)
    $routes->group('unit-holds', function($routes) {
        $routes->get('/', 'UnitHoldController::index', ['filter' => 'permission:unit_holds.view']);
        $routes->post('store', 'UnitHoldController::store', ['filter' => 'permission:unit_holds.create']);
        $routes->post('release/(:num)', 'UnitHoldController::release/$1', ['filter' => 'permission:unit_holds.release']);
    });

    // ========================================================
    // PHASE 4 — REAL ESTATE BOOKINGS, SALES AGREEMENTS,
    // PAYMENT SCHEDULES, INVOICING, RECEIPTS & COMMISSIONS
    // ========================================================

    // Sales Dashboard
    $routes->get('sales-dashboard', 'SalesDashboardController::index', ['filter' => 'permission:sales_dashboard.view']);

    // Customers & KYC Management
    $routes->group('customers', function($routes) {
        $routes->get('/', 'CustomerController::index', ['filter' => 'permission:customers.view']);
        $routes->get('create', 'CustomerController::create', ['filter' => 'permission:customers.create']);
        $routes->post('store', 'CustomerController::store', ['filter' => 'permission:customers.create']);
        $routes->post('convert-lead/(:num)', 'CustomerController::convertLead/$1', ['filter' => 'permission:customers.create']);
        $routes->get('view/(:num)', 'CustomerController::view/$1', ['filter' => 'permission:customers.view']);
        $routes->get('edit/(:num)', 'CustomerController::edit/$1', ['filter' => 'permission:customers.edit']);
        $routes->post('update/(:num)', 'CustomerController::update/$1', ['filter' => 'permission:customers.edit']);
        $routes->post('delete/(:num)', 'CustomerController::delete/$1', ['filter' => 'permission:customers.delete']);
        $routes->post('kyc/upload/(:num)', 'CustomerController::uploadKyc/$1', ['filter' => 'permission:customers.kyc.upload']);
        $routes->post('kyc/verify/(:num)', 'CustomerController::verifyKyc/$1', ['filter' => 'permission:customers.kyc.verify']);
        $routes->get('kyc/document/(:num)', 'CustomerController::viewKycDocument/$1', ['filter' => 'permission:customers.kyc.view']);
    });

    // Bookings & Booking Vouchers
    $routes->group('bookings', function($routes) {
        $routes->get('/', 'BookingController::index', ['filter' => 'permission:bookings.view']);
        $routes->get('create', 'BookingController::create', ['filter' => 'permission:bookings.create']);
        $routes->post('store', 'BookingController::store', ['filter' => 'permission:bookings.create']);
        $routes->get('view/(:num)', 'BookingController::view/$1', ['filter' => 'permission:bookings.view']);
        $routes->get('voucher/(:num)', 'BookingController::voucher/$1', ['filter' => 'permission:bookings.view']);
        $routes->post('confirm/(:num)', 'BookingController::confirm/$1', ['filter' => 'permission:bookings.confirm']);
        $routes->post('cancel/(:num)', 'BookingController::cancel/$1', ['filter' => 'permission:bookings.cancel']);
    });

    // Sales Agreements
    $routes->group('agreements', function($routes) {
        $routes->get('/', 'SalesAgreementController::index', ['filter' => 'permission:agreements.view']);
        $routes->get('create', 'SalesAgreementController::create', ['filter' => 'permission:agreements.create']);
        $routes->post('store', 'SalesAgreementController::store', ['filter' => 'permission:agreements.create']);
        $routes->get('view/(:num)', 'SalesAgreementController::view/$1', ['filter' => 'permission:agreements.view']);
        $routes->post('sign/(:num)', 'SalesAgreementController::sign/$1', ['filter' => 'permission:agreements.sign']);
        $routes->post('cancel/(:num)', 'SalesAgreementController::cancel/$1', ['filter' => 'permission:agreements.cancel']);
    });

    // Payments & Milestones
    $routes->group('payments', function($routes) {
        $routes->get('/', 'PaymentController::index', ['filter' => 'permission:payments.view']);
        $routes->post('store', 'PaymentController::store', ['filter' => 'permission:payments.create']);
        $routes->post('cancel/(:num)', 'PaymentController::cancel/$1', ['filter' => 'permission:payments.cancel']);
    });

    // Invoices
    $routes->group('invoices', function($routes) {
        $routes->get('/', 'InvoiceController::index', ['filter' => 'permission:invoices.view']);
        $routes->get('create', 'InvoiceController::create', ['filter' => 'permission:invoices.create']);
        $routes->post('store', 'InvoiceController::store', ['filter' => 'permission:invoices.create']);
        $routes->get('view/(:num)', 'InvoiceController::view/$1', ['filter' => 'permission:invoices.view']);
        $routes->post('cancel/(:num)', 'InvoiceController::cancel/$1', ['filter' => 'permission:invoices.cancel']);
    });

    // Receipts
    $routes->group('receipts', function($routes) {
        $routes->get('/', 'ReceiptController::index', ['filter' => 'permission:receipts.view']);
        $routes->get('view/(:num)', 'ReceiptController::view/$1', ['filter' => 'permission:receipts.view']);
    });

    // Commissions
    $routes->group('commissions', function($routes) {
        $routes->get('/', 'CommissionController::index', ['filter' => 'permission:commissions.view']);
        $routes->post('rule/store', 'CommissionController::storeRule', ['filter' => 'permission:commissions.create']);
        $routes->post('store', 'CommissionController::store', ['filter' => 'permission:commissions.create']);
        $routes->post('approve/(:num)', 'CommissionController::approve/$1', ['filter' => 'permission:commissions.approve']);
        $routes->post('mark-paid/(:num)', 'CommissionController::markPaid/$1', ['filter' => 'permission:commissions.mark_paid']);
    });

    // ========================================================
    // PHASE 5 — RENTAL & LEASE MANAGEMENT, MAINTENANCE &
    // FACILITY OPS, PORTALS & STATUTORY FINANCIAL COMPLIANCE
    // ========================================================

    // Tenants & Tenant KYC
    $routes->group('tenants', function($routes) {
        $routes->get('/', 'TenantController::index', ['filter' => 'permission:tenants.view']);
        $routes->get('create', 'TenantController::create', ['filter' => 'permission:tenants.create']);
        $routes->post('store', 'TenantController::store', ['filter' => 'permission:tenants.create']);
        $routes->get('view/(:num)', 'TenantController::view/$1', ['filter' => 'permission:tenants.view']);
        $routes->get('edit/(:num)', 'TenantController::edit/$1', ['filter' => 'permission:tenants.edit']);
        $routes->post('update/(:num)', 'TenantController::update/$1', ['filter' => 'permission:tenants.edit']);
        $routes->post('delete/(:num)', 'TenantController::delete/$1', ['filter' => 'permission:tenants.delete']);
        $routes->post('kyc/upload/(:num)', 'TenantController::uploadKyc/$1', ['filter' => 'permission:tenants.kyc']);
        $routes->post('kyc/verify/(:num)', 'TenantController::verifyKyc/$1', ['filter' => 'permission:tenants.kyc']);
        $routes->get('kyc/document/(:num)', 'TenantController::viewKycDocument/$1', ['filter' => 'permission:tenants.kyc']);
    });

    // Lease Agreements
    $routes->group('leases', function($routes) {
        $routes->get('/', 'LeaseAgreementController::index', ['filter' => 'permission:leases.view']);
        $routes->get('create', 'LeaseAgreementController::create', ['filter' => 'permission:leases.create']);
        $routes->post('store', 'LeaseAgreementController::store', ['filter' => 'permission:leases.create']);
        $routes->get('view/(:num)', 'LeaseAgreementController::view/$1', ['filter' => 'permission:leases.view']);
        $routes->get('voucher/(:num)', 'LeaseAgreementController::voucher/$1', ['filter' => 'permission:leases.view']);
        $routes->post('activate/(:num)', 'LeaseAgreementController::activate/$1', ['filter' => 'permission:leases.activate']);
        $routes->post('terminate/(:num)', 'LeaseAgreementController::terminate/$1', ['filter' => 'permission:leases.terminate']);
        $routes->post('renew/(:num)', 'LeaseAgreementController::renew/$1', ['filter' => 'permission:leases.renew']);
    });

    // Security Deposits
    $routes->group('deposits', function($routes) {
        $routes->get('/', 'SecurityDepositController::index', ['filter' => 'permission:deposits.view']);
        $routes->post('refund/(:num)', 'SecurityDepositController::processRefund/$1', ['filter' => 'permission:deposits.manage']);
    });

    // Rent Demands
    $routes->group('rent-demands', function($routes) {
        $routes->get('/', 'RentDemandController::index', ['filter' => 'permission:rent_demands.view']);
        $routes->post('generate', 'RentDemandController::generate', ['filter' => 'permission:rent_demands.create']);
        $routes->get('view/(:num)', 'RentDemandController::view/$1', ['filter' => 'permission:rent_demands.view']);
    });

    // Rent Collections
    $routes->group('rent-collections', function($routes) {
        $routes->get('/', 'RentCollectionController::index', ['filter' => 'permission:rent_collections.view']);
        $routes->post('store', 'RentCollectionController::store', ['filter' => 'permission:rent_collections.create']);
        $routes->get('receipt/(:num)', 'RentCollectionController::receipt/$1', ['filter' => 'permission:rent_collections.view']);
    });

    // Facility Assets
    $routes->group('facility-assets', function($routes) {
        $routes->get('/', 'FacilityAssetController::index', ['filter' => 'permission:facility_assets.view']);
        $routes->post('store', 'FacilityAssetController::store', ['filter' => 'permission:facility_assets.create']);
        $routes->post('update/(:num)', 'FacilityAssetController::update/$1', ['filter' => 'permission:facility_assets.edit']);
        $routes->post('delete/(:num)', 'FacilityAssetController::delete/$1', ['filter' => 'permission:facility_assets.delete']);
    });

    // Technicians
    $routes->group('technicians', function($routes) {
        $routes->get('/', 'TechnicianController::index', ['filter' => 'permission:technicians.view']);
        $routes->post('store', 'TechnicianController::store', ['filter' => 'permission:technicians.create']);
        $routes->post('update/(:num)', 'TechnicianController::update/$1', ['filter' => 'permission:technicians.edit']);
        $routes->post('delete/(:num)', 'TechnicianController::delete/$1', ['filter' => 'permission:technicians.delete']);
    });

    // Maintenance Work Orders
    $routes->group('maintenance', function($routes) {
        $routes->get('/', 'MaintenanceController::index', ['filter' => 'permission:maintenance.view']);
        $routes->get('create', 'MaintenanceController::create', ['filter' => 'permission:maintenance.create']);
        $routes->post('store', 'MaintenanceController::store', ['filter' => 'permission:maintenance.create']);
        $routes->get('view/(:num)', 'MaintenanceController::view/$1', ['filter' => 'permission:maintenance.view']);
        $routes->get('voucher/(:num)', 'MaintenanceController::workOrderVoucher/$1', ['filter' => 'permission:maintenance.view']);
        $routes->post('status/(:num)', 'MaintenanceController::updateStatus/$1', ['filter' => 'permission:maintenance.manage']);
        $routes->post('assign/(:num)', 'MaintenanceController::assignTechnician/$1', ['filter' => 'permission:maintenance.manage']);
    });

    // Complaints
    $routes->group('complaints', function($routes) {
        $routes->get('/', 'MaintenanceController::complaints', ['filter' => 'permission:complaints.view']);
        $routes->post('store', 'MaintenanceController::storeComplaint', ['filter' => 'permission:complaints.create']);
        $routes->post('status/(:num)', 'MaintenanceController::updateComplaintStatus/$1', ['filter' => 'permission:complaints.manage']);
        $routes->post('feedback/(:num)', 'MaintenanceController::submitFeedback/$1', ['filter' => 'permission:complaints.create']);
    });

    // Preventive Maintenance / AMC
    $routes->group('preventive-maintenance', function($routes) {
        $routes->get('/', 'MaintenanceController::preventive', ['filter' => 'permission:preventive.view']);
        $routes->post('store', 'MaintenanceController::storePreventive', ['filter' => 'permission:preventive.create']);
        $routes->post('complete/(:num)', 'MaintenanceController::completePreventive/$1', ['filter' => 'permission:preventive.manage']);
    });

    // CAM Charges
    $routes->group('cam-charges', function($routes) {
        $routes->get('/', 'MaintenanceController::cam', ['filter' => 'permission:cam.view']);
        $routes->post('store', 'MaintenanceController::storeCam', ['filter' => 'permission:cam.create']);
        $routes->post('status/(:num)', 'MaintenanceController::updateCamStatus/$1', ['filter' => 'permission:cam.manage']);
    });

    // Dedicated Portals
    $routes->group('portal', function($routes) {
        $routes->get('owner', 'PortalController::owner', ['filter' => 'permission:portal.owner']);
        $routes->get('tenant', 'PortalController::tenant', ['filter' => 'permission:portal.tenant']);
        $routes->get('partner', 'PortalController::partner', ['filter' => 'permission:portal.partner']);
        $routes->post('request/submit', 'PortalController::submitRequest');
    });

    // Statutory Financial Compliance Reports & Executive Reports Hub
    $routes->group('reports', function($routes) {
        $routes->get('/', 'ReportHubController::index', ['filter' => 'permission:reports.view']);
        $routes->get('sales', 'ReportHubController::sales', ['filter' => 'permission:reports.sales']);
        $routes->get('rental', 'ReportHubController::rental', ['filter' => 'permission:reports.rental']);
        $routes->get('gst', 'FinanceReportController::gst', ['filter' => 'permission:reports.gst']);
        $routes->get('tds', 'FinanceReportController::tds', ['filter' => 'permission:reports.tds']);
        $routes->post('tds/store', 'FinanceReportController::storeTds', ['filter' => 'permission:reports.tds']);
        $routes->post('tds/certificate/(:num)', 'FinanceReportController::generateCertificate/$1', ['filter' => 'permission:reports.tds']);
        $routes->get('receivables', 'FinanceReportController::receivables', ['filter' => 'permission:reports.receivables']);
    });

    // ========================================================
    // PHASE 6 — CONSTRUCTION MANAGEMENT, CONTRACTORS,
    // PROCUREMENT, QUALITY INSPECTIONS & POSSESSION HANDOVER
    // ========================================================

    // Construction Dashboard, Milestones & Daily Logs
    $routes->group('construction', function($routes) {
        $routes->get('dashboard', 'ConstructionController::dashboard', ['filter' => 'permission:construction.dashboard.view']);
        $routes->get('milestones', 'ConstructionController::milestones', ['filter' => 'permission:construction.milestones.view']);
        $routes->post('milestones/store', 'ConstructionController::storeMilestone', ['filter' => 'permission:construction.milestones.manage']);
        $routes->post('milestones/update/(:num)', 'ConstructionController::updateMilestone/$1', ['filter' => 'permission:construction.milestones.manage']);
        
        $routes->get('daily-logs', 'ConstructionController::dailyLogs', ['filter' => 'permission:construction.daily_logs.view']);
        $routes->get('daily-logs/create', 'ConstructionController::createDailyLog', ['filter' => 'permission:construction.daily_logs.create']);
        $routes->post('daily-logs/store', 'ConstructionController::storeDailyLog', ['filter' => 'permission:construction.daily_logs.create']);
        $routes->post('daily-logs/approve/(:num)', 'ConstructionController::approveDailyLog/$1', ['filter' => 'permission:construction.daily_logs.approve']);

        $routes->get('work-orders', 'ContractorController::workOrders', ['filter' => 'permission:contractors.work_orders.view']);
        $routes->post('work-orders/store', 'ContractorController::storeWorkOrder', ['filter' => 'permission:contractors.work_orders.create']);
        $routes->post('work-orders/status/(:num)', 'ContractorController::updateWorkOrderStatus/$1', ['filter' => 'permission:contractors.work_orders.edit']);
    });

    // Contractors & Vendors
    $routes->group('contractors', function($routes) {
        $routes->get('/', 'ContractorController::index', ['filter' => 'permission:contractors.view']);
        $routes->get('create', 'ContractorController::create', ['filter' => 'permission:contractors.create']);
        $routes->post('store', 'ContractorController::store', ['filter' => 'permission:contractors.create']);
        $routes->get('edit/(:num)', 'ContractorController::edit/$1', ['filter' => 'permission:contractors.edit']);
        $routes->post('update/(:num)', 'ContractorController::update/$1', ['filter' => 'permission:contractors.edit']);
        $routes->post('delete/(:num)', 'ContractorController::delete/$1', ['filter' => 'permission:contractors.delete']);
    });

    // Material Procurement & Indents
    $routes->group('procurement', function($routes) {
        $routes->get('/', 'ProcurementController::index', ['filter' => 'permission:procurement.view']);
        $routes->get('create', 'ProcurementController::create', ['filter' => 'permission:procurement.create']);
        $routes->post('store', 'ProcurementController::store', ['filter' => 'permission:procurement.create']);
        $routes->post('approve/(:num)', 'ProcurementController::approve/$1', ['filter' => 'permission:procurement.approve']);
        $routes->post('procure/(:num)', 'ProcurementController::procure/$1', ['filter' => 'permission:procurement.approve']);
    });

    // Site Quality & Safety Inspections
    $routes->group('inspections', function($routes) {
        $routes->get('/', 'SiteInspectionController::index', ['filter' => 'permission:inspections.view']);
        $routes->get('create', 'SiteInspectionController::create', ['filter' => 'permission:inspections.create']);
        $routes->post('store', 'SiteInspectionController::store', ['filter' => 'permission:inspections.create']);
        $routes->post('rectify/(:num)', 'SiteInspectionController::rectify/$1', ['filter' => 'permission:inspections.conduct']);
    });

    // Possession & Key Handover Management
    $routes->group('handover', function($routes) {
        $routes->get('/', 'HandoverController::index', ['filter' => 'permission:handover.view']);
        $routes->get('create', 'HandoverController::create', ['filter' => 'permission:handover.create']);
        $routes->post('store', 'HandoverController::store', ['filter' => 'permission:handover.create']);
        $routes->get('certificate/(:num)', 'HandoverController::certificate/$1', ['filter' => 'permission:handover.certificate']);
    });

    // ========================================================
    // PHASE 7 — ERP COMPLETENESS, LANDLORD/OWNER SUITE,
    // AGENT BROKERAGE, EXPENSES, MARKETING, COMPLIANCE,
    // NOTIFICATIONS, INTERACTIONS & SYSTEM CONFIGURATION
    // ========================================================

    // Property Owners & Landlord Portfolio
    $routes->group('owners', function($routes) {
        $routes->get('/', 'PropertyOwnerController::index', ['filter' => 'permission:owners.view']);
        $routes->get('create', 'PropertyOwnerController::create', ['filter' => 'permission:owners.create']);
        $routes->post('store', 'PropertyOwnerController::store', ['filter' => 'permission:owners.create']);
        $routes->get('view/(:num)', 'PropertyOwnerController::show/$1', ['filter' => 'permission:owners.view']);
        $routes->get('edit/(:num)', 'PropertyOwnerController::edit/$1', ['filter' => 'permission:owners.edit']);
        $routes->post('update/(:num)', 'PropertyOwnerController::update/$1', ['filter' => 'permission:owners.edit']);
        $routes->post('delete/(:num)', 'PropertyOwnerController::delete/$1', ['filter' => 'permission:owners.delete']);
        $routes->get('statement/(:num)', 'PropertyOwnerController::statement/$1', ['filter' => 'permission:owners.view']);
    });

    // Agents & External Brokers
    $routes->group('agents', function($routes) {
        $routes->get('/', 'AgentController::index', ['filter' => 'permission:agents.view']);
        $routes->get('create', 'AgentController::create', ['filter' => 'permission:agents.create']);
        $routes->post('store', 'AgentController::store', ['filter' => 'permission:agents.create']);
        $routes->get('view/(:num)', 'AgentController::show/$1', ['filter' => 'permission:agents.view']);
        $routes->get('edit/(:num)', 'AgentController::edit/$1', ['filter' => 'permission:agents.edit']);
        $routes->post('update/(:num)', 'AgentController::update/$1', ['filter' => 'permission:agents.edit']);
        $routes->post('delete/(:num)', 'AgentController::delete/$1', ['filter' => 'permission:agents.delete']);
    });

    // Property Expenses & Operating Outlays
    $routes->group('expenses', function($routes) {
        $routes->get('/', 'PropertyExpenseController::index', ['filter' => 'permission:expenses.view']);
        $routes->get('create', 'PropertyExpenseController::create', ['filter' => 'permission:expenses.create']);
        $routes->post('store', 'PropertyExpenseController::store', ['filter' => 'permission:expenses.create']);
        $routes->get('view/(:num)', 'PropertyExpenseController::show/$1', ['filter' => 'permission:expenses.view']);
        $routes->get('edit/(:num)', 'PropertyExpenseController::edit/$1', ['filter' => 'permission:expenses.edit']);
        $routes->post('update/(:num)', 'PropertyExpenseController::update/$1', ['filter' => 'permission:expenses.edit']);
        $routes->post('delete/(:num)', 'PropertyExpenseController::delete/$1', ['filter' => 'permission:expenses.delete']);
        $routes->get('categories', 'PropertyExpenseController::categories', ['filter' => 'permission:expenses.view']);
        $routes->post('categories/store', 'PropertyExpenseController::storeCategory', ['filter' => 'permission:expenses.create']);
        $routes->get('report', 'PropertyExpenseController::report', ['filter' => 'permission:expenses.report']);
    });

    // Marketing & Advertising Campaigns
    $routes->group('campaigns', function($routes) {
        $routes->get('/', 'MarketingCampaignController::index', ['filter' => 'permission:campaigns.view']);
        $routes->get('create', 'MarketingCampaignController::create', ['filter' => 'permission:campaigns.create']);
        $routes->post('store', 'MarketingCampaignController::store', ['filter' => 'permission:campaigns.create']);
        $routes->get('view/(:num)', 'MarketingCampaignController::show/$1', ['filter' => 'permission:campaigns.view']);
        $routes->get('edit/(:num)', 'MarketingCampaignController::edit/$1', ['filter' => 'permission:campaigns.edit']);
        $routes->post('update/(:num)', 'MarketingCampaignController::update/$1', ['filter' => 'permission:campaigns.edit']);
        $routes->post('delete/(:num)', 'MarketingCampaignController::delete/$1', ['filter' => 'permission:campaigns.delete']);
    });

    // Property Documents & Compliance Repository
    $routes->group('documents', function($routes) {
        $routes->get('/', 'PropertyDocumentController::index', ['filter' => 'permission:documents.view']);
        $routes->get('create', 'PropertyDocumentController::create', ['filter' => 'permission:documents.create']);
        $routes->post('store', 'PropertyDocumentController::store', ['filter' => 'permission:documents.create']);
        $routes->get('view/(:num)', 'PropertyDocumentController::show/$1', ['filter' => 'permission:documents.view']);
        $routes->get('edit/(:num)', 'PropertyDocumentController::edit/$1', ['filter' => 'permission:documents.edit']);
        $routes->post('update/(:num)', 'PropertyDocumentController::update/$1', ['filter' => 'permission:documents.edit']);
        $routes->post('delete/(:num)', 'PropertyDocumentController::delete/$1', ['filter' => 'permission:documents.delete']);
        $routes->post('verify/(:num)', 'PropertyDocumentController::verify/$1', ['filter' => 'permission:documents.verify']);
        $routes->get('download/(:num)', 'PropertyDocumentController::download/$1', ['filter' => 'permission:documents.view']);
    });

    // Property Legal & Statutory Verifications
    $routes->group('verifications', function($routes) {
        $routes->get('/', 'PropertyVerificationController::index', ['filter' => 'permission:verifications.view']);
        $routes->get('create', 'PropertyVerificationController::create', ['filter' => 'permission:verifications.create']);
        $routes->post('store', 'PropertyVerificationController::store', ['filter' => 'permission:verifications.create']);
        $routes->get('view/(:num)', 'PropertyVerificationController::show/$1', ['filter' => 'permission:verifications.view']);
        $routes->post('conduct/(:num)', 'PropertyVerificationController::conduct/$1', ['filter' => 'permission:verifications.conduct']);
    });

    // Centralized Notifications & Reminders
    $routes->group('notifications', function($routes) {
        $routes->get('/', 'NotificationController::index', ['filter' => 'permission:notifications.view']);
        $routes->get('mark-read/(:num)', 'NotificationController::markAsRead/$1', ['filter' => 'permission:notifications.view']);
        $routes->post('mark-read/(:num)', 'NotificationController::markAsRead/$1', ['filter' => 'permission:notifications.view']);
        $routes->get('mark-all-read', 'NotificationController::markAllAsRead', ['filter' => 'permission:notifications.view']);
        $routes->post('mark-all-read', 'NotificationController::markAllAsRead', ['filter' => 'permission:notifications.view']);
        $routes->post('create', 'NotificationController::create', ['filter' => 'permission:notifications.create']);
    });

    // Customer Communication History & Logs
    $routes->group('communications', function($routes) {
        $routes->get('/', 'CustomerCommunicationController::index', ['filter' => 'permission:communications.view']);
        $routes->post('store', 'CustomerCommunicationController::store', ['filter' => 'permission:communications.create']);
    });

    // System Settings & ERP Configuration
    $routes->group('settings', function($routes) {
        $routes->get('/', 'SettingController::index', ['filter' => 'permission:settings.view']);
        $routes->post('update', 'SettingController::update', ['filter' => 'permission:settings.edit']);
        $routes->get('export', 'SettingController::exportData', ['filter' => 'permission:settings.export']);
        $routes->post('backup', 'SettingController::backupDatabase', ['filter' => 'permission:settings.backup']);
    });
});


