<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\BranchModel;
use App\Models\AuditLogModel;

class DashboardController extends BaseController
{
    /**
     * Display Admin Foundation Dashboard
     */
    public function index()
    {
        $userModel     = new UserModel();
        $roleModel     = new RoleModel();
        $branchModel   = new BranchModel();
        $auditLogModel = new AuditLogModel();

        // 1. Dynamic foundation metrics strictly from MySQL
        $totalUsers    = $userModel->countAllResults();
        $activeUsers   = $userModel->where('status', 'active')->countAllResults();
        $totalRoles    = $roleModel->countAllResults();
        $totalBranches = $branchModel->countAllResults();

        // 2. Phase 2 Real Estate Foundation Metrics from MySQL
        $propertyModel = new \App\Models\PropertyModel();
        $projectModel  = new \App\Models\ProjectModel();
        $unitModel     = new \App\Models\PropertyUnitModel();

        $totalProperties     = $propertyModel->countAllResults();
        $availableProperties = $propertyModel->where('status', 'Available')->countAllResults();
        $reservedProperties  = $propertyModel->where('status', 'Reserved')->countAllResults();
        $bookedProperties    = $propertyModel->where('status', 'Booked')->countAllResults();
        $soldProperties      = $propertyModel->where('status', 'Sold')->countAllResults();
        $rentedProperties    = $propertyModel->where('status', 'Rented')->countAllResults();

        $totalProjects  = $projectModel->countAllResults();
        $totalUnits     = $unitModel->countAllResults();
        $availableUnits = $unitModel->where('availability_status', 'Available')->countAllResults();

        // 3. Phase 3 CRM & Sales Pipeline Metrics from MySQL
        $leadModel     = new \App\Models\LeadModel();
        $enquiryModel  = new \App\Models\EnquiryModel();
        $followupModel = new \App\Models\LeadFollowupModel();
        $visitModel    = new \App\Models\SiteVisitModel();
        $holdModel     = new \App\Models\UnitHoldModel();

        $holdModel->expireOverdueHolds(); // Keep counts accurate

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        $totalLeads       = $leadModel->where('deleted_at', null)->countAllResults();
        $newLeads         = $leadModel->where('lead_status', 'New')->where('deleted_at', null)->countAllResults();
        $qualifiedLeads   = $leadModel->where('lead_status', 'Qualified')->where('deleted_at', null)->countAllResults();
        $leadsWon         = $leadModel->where('lead_stage', 'Won')->where('deleted_at', null)->countAllResults();
        $leadsLost        = $leadModel->where('lead_status', 'Lost')->where('deleted_at', null)->countAllResults();

        $openEnquiries    = $enquiryModel->where('status', 'Open')->countAllResults();
        $todayFollowups   = $followupModel->getTodayPendingCount();
        $overdueFollowups = $followupModel->getOverdueCount();
        $todayVisits      = $visitModel->getTodayVisitsCount();
        $upcomingVisits   = $visitModel->whereIn('status', ['Scheduled', 'Confirmed'])->where('scheduled_at >', $todayEnd)->countAllResults();
        $activeUnitHolds  = $holdModel->where('hold_status', 'Active')->countAllResults();

        // 4. Recent Logins (Users with recent login timestamps)
        $recentLogins = $userModel->select('users.*, branches.name as branch_name')
                                  ->join('branches', 'branches.id = users.branch_id', 'left')
                                  ->where('users.last_login_at IS NOT NULL')
                                  ->orderBy('users.last_login_at', 'DESC')
                                  ->limit(6)
                                  ->find();

        // 5. Recent Audit Activities
        $recentActivities = $auditLogModel->select('audit_logs.*, users.name as user_name')
                                          ->join('users', 'users.id = audit_logs.user_id', 'left')
                                          ->orderBy('audit_logs.id', 'DESC')
                                          ->limit(8)
                                          ->find();

        // 6. Phase 4 Sales & Financial Transaction Metrics from MySQL
        $bookingModel = new \App\Models\BookingModel();
        $salesSummary = $bookingModel->getSalesSummary();

        $data = [
            'title'               => 'Executive Dashboard - Real Estate ERP',
            'totalUsers'          => $totalUsers,
            'activeUsers'         => $activeUsers,
            'totalRoles'          => $totalRoles,
            'totalBranches'       => $totalBranches,
            'totalProperties'     => $totalProperties,
            'availableProperties' => $availableProperties,
            'reservedProperties'  => $reservedProperties,
            'bookedProperties'    => $bookedProperties,
            'soldProperties'      => $soldProperties,
            'rentedProperties'    => $rentedProperties,
            'totalProjects'       => $totalProjects,
            'totalUnits'          => $totalUnits,
            'availableUnits'      => $availableUnits,
            'totalLeads'          => $totalLeads,
            'newLeads'            => $newLeads,
            'qualifiedLeads'      => $qualifiedLeads,
            'leadsWon'            => $leadsWon,
            'leadsLost'           => $leadsLost,
            'openEnquiries'       => $openEnquiries,
            'todayFollowups'      => $todayFollowups,
            'overdueFollowups'    => $overdueFollowups,
            'todayVisits'         => $todayVisits,
            'upcomingVisits'      => $upcomingVisits,
            'activeUnitHolds'     => $activeUnitHolds,
            'recentLogins'        => $recentLogins,
            'recentActivities'    => $recentActivities,
            'salesSummary'        => $salesSummary,
        ];

        return view('dashboard/index', $data);
    }
}
