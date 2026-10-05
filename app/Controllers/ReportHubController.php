<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\BookingModel;
use App\Models\PaymentModel;
use App\Models\PropertyExpenseModel;
use App\Models\MarketingCampaignModel;

class ReportHubController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // High-level executive figures from real DB
        $totalBookingsValue = (float)($db->table('bookings')->where('booking_status !=', 'Cancelled')->selectSum('final_amount', 'total')->get()->getRow()->total ?? 0);
        $totalCollected     = (float)($db->table('payments')->where('status', 'Completed')->selectSum('amount', 'total')->get()->getRow()->total ?? 0);
        $totalExpenses      = (float)($db->table('property_expenses')->where('deleted_at IS NULL')->selectSum('total_amount', 'total')->get()->getRow()->total ?? 0);
        $totalCommissions   = (float)($db->table('commissions')->where('status', 'Paid')->selectSum('commission_amount', 'total')->get()->getRow()->total ?? 0);

        // Occupancy calculation
        $totalUnits = (int)$db->table('property_units')->where('deleted_at IS NULL')->countAllResults();
        $occupiedUnits = (int)$db->table('property_units')->whereIn('availability_status', ['Booked', 'Sold', 'Rented'])->where('deleted_at IS NULL')->countAllResults();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        return view('reports/index', [
            'title'              => 'Executive Reports & Analytics Hub',
            'totalBookingsValue' => $totalBookingsValue,
            'totalCollected'     => $totalCollected,
            'totalExpenses'      => $totalExpenses,
            'totalCommissions'   => $totalCommissions,
            'totalUnits'         => $totalUnits,
            'occupiedUnits'      => $occupiedUnits,
            'occupancyRate'      => $occupancyRate,
        ]);
    }

    public function sales()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $db   = \Config\Database::connect();

        $bookings = $db->table('bookings')
            ->select('bookings.*, customers.first_name, customers.last_name, property_units.unit_number, properties.title as property_title')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->join('property_units', 'property_units.id = bookings.property_unit_id', 'left')
            ->join('properties', 'properties.id = property_units.property_id', 'left')
            ->where('YEAR(bookings.booking_date)', $year)
            ->where('bookings.deleted_at IS NULL')
            ->orderBy('bookings.booking_date', 'DESC')
            ->get()->getResultArray();

        $totalSales = array_sum(array_column($bookings, 'final_amount'));
        $totalTokens= array_sum(array_column($bookings, 'token_amount'));

        return view('reports/sales', [
            'title'      => "Property Sales & Revenue Report ({$year})",
            'bookings'   => $bookings,
            'totalSales' => $totalSales,
            'totalTokens'=> $totalTokens,
            'year'       => $year,
        ]);
    }

    public function rental()
    {
        $db = \Config\Database::connect();

        $leases = $db->table('lease_agreements')
            ->select('lease_agreements.*, tenants.full_name as tenant_fname, property_units.unit_number, properties.title as property_title')
            ->join('tenants', 'tenants.id = lease_agreements.tenant_id', 'left')
            ->join('property_units', 'property_units.id = lease_agreements.property_unit_id', 'left')
            ->join('properties', 'properties.id = property_units.property_id', 'left')
            ->where('lease_agreements.deleted_at IS NULL')
            ->orderBy('lease_agreements.id', 'DESC')
            ->get()->getResultArray();

        $collections = (float)($db->table('rent_collections')->selectSum('amount', 'total')->get()->getRow()->total ?? 0);
        $demands     = (float)($db->table('rent_demands')->selectSum('total_amount', 'total')->get()->getRow()->total ?? 0);

        return view('reports/rental', [
            'title'       => 'Rental Performance & Lease Occupancy Report',
            'leases'      => $leases,
            'collections' => $collections,
            'demands'     => $demands,
        ]);
    }
}
