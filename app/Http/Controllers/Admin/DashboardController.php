<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\ServiceRequest;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Announcement;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Use a single cache key for all dashboard data
        $dashboardData = Cache::remember('dashboard.full_data_v2', 300, function () {
            return $this->getDashboardData();
        });

        // 🔹 Only get real-time data that changes frequently
        $currentTime = now()->format('H:i:s');
        
        // 🔹 Optimized pending requests with only needed relationships
        $pendingRequests = ServiceRequest::with([
                'customer.user:id,name', 
                'technician:id,full_name'
            ])
            ->where('status', 'pending')
            ->latest()
            ->paginate(5, ['*'], 'pending_page');

        return view('admin.dashboard', array_merge($dashboardData, [
            'currentTime' => $currentTime,
            'pendingRequests' => $pendingRequests,
        ]));
    }

    private function getDashboardData()
    {
        // 🔹 Get ALL counts in a single raw query (massive performance boost)
       $counts = DB::selectOne("
    SELECT 
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT COUNT(*) FROM technicians WHERE status = 'active') as active_technicians,
        (SELECT COUNT(*) FROM service_requests) as total_service_requests,
        (SELECT COUNT(*) FROM subscriptions WHERE status = 'active') as active_subscriptions,
        (SELECT COUNT(*) FROM invoices WHERE status != 'paid') as pending_invoices,
        (SELECT COUNT(*) FROM service_requests WHERE status = 'completed') as completed_requests,
        (SELECT COUNT(*) FROM technicians) as total_technicians,
        (SELECT COUNT(*) FROM subscriptions) as total_subscriptions,
        (SELECT AVG(EXTRACT(EPOCH FROM (assigned_at - created_at)) / 3600) 
         FROM service_requests WHERE assigned_at IS NOT NULL) as avg_response_time,
        (SELECT AVG(rating) * 20 FROM service_requests WHERE rating IS NOT NULL) as customer_satisfaction,
        (SELECT SUM(amount_paid)
         FROM payments
         WHERE status = 'paid'
         AND EXTRACT(YEAR FROM payment_date) = EXTRACT(YEAR FROM NOW())
         AND EXTRACT(MONTH FROM payment_date) = EXTRACT(MONTH FROM NOW())) as revenue_this_month
");


        // 🔹 Get monthly growth in separate optimized query
        $monthlyGrowth = $this->getMonthlyGrowth();

        // 🔹 Get chart data
        $charts = $this->getChartData();

        // 🔹 Get lists with optimized queries
        $lists = $this->getOptimizedLists();

        // 🔹 Calculate trends
        $trends = $this->calculateTrends($counts);

        // 🔹 Recent activity
        $recentActivity = $this->getRecentActivity();

        return [
            // Counts
            'totalCustomers' => $counts->total_customers,
            'activeTechnicians' => $counts->active_technicians,
            'totalServiceRequests' => $counts->total_service_requests,
            'activeSubscriptions' => $counts->active_subscriptions,
            'pendingInvoices' => $counts->pending_invoices,
            
            // Metrics
            'avgResponseTime' => round($counts->avg_response_time ?? 0, 1),
            'customerSatisfaction' => round($counts->customer_satisfaction ?? 0, 1),
            'revenueThisMonth' => $counts->revenue_this_month ?? 0,
            'monthlyGrowth' => $monthlyGrowth,
            'systemUptime' => 99.9,
            
            // Charts
            'monthlyRevenueData' => $charts['monthlyRevenueData'],
            'requestsBreakdown' => $charts['requestsBreakdown'],
            
            // Lists
            'latestPayments' => $lists['latestPayments'],
            'technicianReports' => $lists['technicianReports'],
            'expiringSubscriptions' => $lists['expiringSubscriptions'],
            'announcements' => $lists['announcements'],
            'topCustomers' => $lists['topCustomers'],
            
            // Activity & Trends
            'recentActivity' => $recentActivity,
            'customerTrend' => $trends['customerTrend'],
            'technicianTrend' => $trends['technicianTrend'],
            'serviceRequestTrend' => $trends['serviceRequestTrend'],
            'subscriptionTrend' => $trends['subscriptionTrend'],
        ];
    }

    private function getMonthlyGrowth()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        $currentCount = Customer::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $previousCount = Customer::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)
            ->count();

        return $previousCount > 0 ? (($currentCount - $previousCount) / $previousCount) * 100 : 0;
    }

    private function getChartData()
    {
        // Monthly Revenue
       $monthlyRevenueData = DB::table('payments')
    ->selectRaw('EXTRACT(MONTH FROM payment_date) AS month, SUM(amount_paid) AS total')
    ->where('status', 'paid')
    ->whereRaw('EXTRACT(YEAR FROM payment_date) = ?', [now()->year])
    ->groupByRaw('EXTRACT(MONTH FROM payment_date)')
    ->pluck('total', 'month')
    ->toArray();


        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $formattedRevenue = [];
        foreach ($months as $index => $month) {
            $formattedRevenue[$month] = $monthlyRevenueData[$index + 1] ?? 0;
        }

        // Requests breakdown
        $requestsBreakdown = DB::table('service_requests')
            ->select('service_type', DB::raw('COUNT(*) as total'))
            ->groupBy('service_type')
            ->pluck('total', 'service_type')
            ->toArray();

        return [
            'monthlyRevenueData' => $formattedRevenue,
            'requestsBreakdown' => $requestsBreakdown,
        ];
    }

    private function getOptimizedLists()
    {
        // Latest Payments with optimized relationships
        $latestPayments = Payment::with([
                'invoice.customer.user:id,name'
            ])
            ->select(['id', 'invoice_id', 'amount_paid', 'payment_date', 'status'])
            ->latest('payment_date')
            ->paginate(5, ['*'], 'payment_page');

        // Technician Reports - optimized with single query
        $technicianReports = Technician::select(['id', 'full_name'])
            ->withCount([
                'serviceRequests as total_requests',
                'serviceRequests as completed_requests' => function ($q) {
                    $q->where('status', 'completed');
                },
                'serviceRequests as pending_requests' => function ($q) {
                    $q->where('status', 'pending');
                }
            ])
            ->orderBy('full_name')
            ->paginate(5);

        // Expiring Subscriptions
        $expiringSubscriptions = Subscription::with([
                'customer.user:id,name',
                'package:id,name'
            ])
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->get(['id', 'customer_id', 'package_id', 'end_date']);

        // Announcements
        $announcements = Announcement::where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'content', 'priority', 'status', 'audience', 'created_at']);

        // Top Customers
        $topCustomers = DB::table('customers')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('subscriptions', 'customers.id', '=', 'subscriptions.customer_id')
            ->join('invoices', 'subscriptions.id', '=', 'invoices.subscription_id')
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('payments.status', 'paid')
            ->select('users.name', DB::raw('SUM(payments.amount_paid) as total_paid'))
            ->groupBy('customers.id', 'users.name')
            ->orderByDesc('total_paid')
            ->limit(5)
            ->get();

        return [
            'latestPayments' => $latestPayments,
            'technicianReports' => $technicianReports,
            'expiringSubscriptions' => $expiringSubscriptions,
            'announcements' => $announcements,
            'topCustomers' => $topCustomers,
        ];
    }

    private function calculateTrends($counts)
    {
        // Simplified trend calculations
        $lastMonthCustomers = Customer::where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $customerTrend = $lastMonthCustomers > 0 
            ? (($counts->total_customers - $lastMonthCustomers) / $lastMonthCustomers) * 100 
            : 0;

        $technicianTrend = $counts->total_technicians > 0 
            ? (($counts->active_technicians / $counts->total_technicians) * 100) - 100 
            : 0;

        $serviceRequestTrend = $counts->total_service_requests > 0 
            ? (($counts->completed_requests / $counts->total_service_requests) * 100) - 50 
            : 0;

        $subscriptionTrend = $counts->total_subscriptions > 0 
            ? (($counts->active_subscriptions / $counts->total_subscriptions) * 100) - 80 
            : 0;

        return [
            'customerTrend' => round($customerTrend, 1),
            'technicianTrend' => round($technicianTrend, 1),
            'serviceRequestTrend' => round($serviceRequestTrend, 1),
            'subscriptionTrend' => round($subscriptionTrend, 1),
        ];
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Recent customers
        $recentCustomers = Customer::with('user:id,name')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(2)
            ->get()
            ->map(fn($c) => [
                'type' => 'customer',
                'message' => 'New customer registered: ' . ($c->user->name ?? 'Unknown'),
                'time' => $c->created_at->diffForHumans(),
                'color' => 'green',
                'timestamp' => $c->created_at
            ]);

        // Recent payments
        $recentPayments = Payment::with('invoice.customer.user:id,name')
            ->where('payment_date', '>=', now()->subDays(7))
            ->latest('payment_date')
            ->take(2)
            ->get()
            ->map(fn($p) => [
                'type' => 'payment',
                'message' => 'Payment received from ' . ($p->invoice->customer->user->name ?? 'Unknown'),
                'time' => $p->payment_date->diffForHumans(),
                'color' => 'purple',
                'timestamp' => $p->payment_date
            ]);

        return $recentCustomers->concat($recentPayments)
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();
    }
}