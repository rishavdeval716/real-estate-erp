<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\PaymentModel;
use App\Models\SalesAgreementModel;
use App\Models\CustomerModel;
use App\Models\PropertyUnitModel;

class SalesDashboardController extends BaseController
{
    public function index()
    {
        $bookingModel = new BookingModel();
        $paymentModel = new PaymentModel();
        $db = \Config\Database::connect();

        $salesSummary = $bookingModel->getSalesSummary();

        // Recent Bookings
        $recentBookings = $bookingModel->select('bookings.*, customers.first_name, customers.last_name, property_units.unit_number, properties.title as property_title')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('properties', 'properties.id = bookings.property_id', 'left')
            ->where('bookings.deleted_at', null)
            ->orderBy('bookings.id', 'DESC')
            ->limit(5)
            ->findAll();

        // Recent Payments
        $recentPayments = $paymentModel->select('payments.*, bookings.booking_number, customers.first_name, customers.last_name')
            ->join('bookings', 'bookings.id = payments.booking_id', 'left')
            ->join('customers', 'customers.id = payments.customer_id', 'left')
            ->orderBy('payments.id', 'DESC')
            ->limit(5)
            ->findAll();

        // Upcoming Milestone Due Items (next 30 days)
        $today = date('Y-m-d');
        $next30Days = date('Y-m-d', strtotime('+30 days'));
        $upcomingMilestones = $db->table('payment_schedule_items')
            ->select('payment_schedule_items.*, bookings.booking_number, customers.first_name, customers.last_name, customers.phone')
            ->join('payment_schedules', 'payment_schedules.id = payment_schedule_items.payment_schedule_id', 'inner')
            ->join('bookings', 'bookings.id = payment_schedules.booking_id', 'inner')
            ->join('customers', 'customers.id = bookings.customer_id', 'inner')
            ->where('payment_schedule_items.due_date >=', $today)
            ->where('payment_schedule_items.due_date <=', $next30Days)
            ->where('payment_schedule_items.status !=', 'Paid')
            ->orderBy('payment_schedule_items.due_date', 'ASC')
            ->limit(8)
            ->get()
            ->getResultArray();

        return view('dashboard/sales', [
            'title'              => 'Sales & Financial Transaction Dashboard',
            'summary'            => $salesSummary,
            'recentBookings'     => $recentBookings,
            'recentPayments'     => $recentPayments,
            'upcomingMilestones' => $upcomingMilestones,
        ]);
    }
}
