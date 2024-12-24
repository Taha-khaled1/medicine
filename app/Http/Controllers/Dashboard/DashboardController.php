<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Job;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:الصفحه الرئيسيه', ['only' => ['main', 'getStatistics']]);
    }
    public function main()
    {
        $totalProductCompleted = Job::where('status', "completed")->count();
        $totalProductUnderway = Job::where('status', "underway")->count();
        $totalProductOpen = Job::where('status', "open")->count();
        $totalProductCanceled = Job::where('status', "canceled")->count();
        $totalUsers = User::count();
        $totalRegularUsers = User::where('type', "user")->count();
        $totalVendors = User::where('type', "vendor")->count();



        //########################################################################################################################################################//
        $oneYearAgo = now()->subYear()->startOfMonth(); // Start from one year ago

        // Query to get the number of users created per month
        $statistics = DB::table('users')
            ->whereDate('created_at', '>=', $oneYearAgo)
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(id) as user_count'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyData = array_fill_keys($monthNames, 0); // Initialize all months with 0 count

        foreach ($statistics as $statistic) {
            $monthName = $monthNames[$statistic->month - 1]; // Convert month number to name
            $monthlyData[$monthName] = $statistic->user_count;
        }

        $labels = array_keys($monthlyData);
        $userCounts = array_values($monthlyData);

        $colors = array_map(function () {
            return '#' . substr(md5(rand()), 0, 6); // Generate a random color for each month
        }, $monthNames);

        // Create the chart
        $chartjs = app()->chartjs
            ->name('lineChartUsers')
            ->type('line')
            ->size(['width' => 600, 'height' => 400])
            ->labels($labels)
            ->datasets([
                [
                    "label" => "عدد مستخدمي التطبيق",
                    'backgroundColor' => $colors, // Use generated colors
                    'borderColor' => $colors, // Use the same colors for borders
                    'data' => $userCounts,
                ]
            ])
            ->options([
                'scales' => [
                    'yAxes' => [
                        [
                            'ticks' => [
                                'beginAtZero' => true,
                            ],
                        ],
                    ],
                ],
                'tooltips' => [
                    'enabled' => true,
                ],
                'responsive' => true,
                'maintainAspectRatio' => false,
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ]);


        return view('dashboard.home.index', [
            'totalProductCompleted' => $totalProductCompleted,
            'totalProductUnderway' => $totalProductUnderway,
            'totalProductOpen' => $totalProductOpen,
            'totalProductCanceled' => $totalProductCanceled,
            'totalUsers' => $totalUsers,
            'totalRegularUsers' => $totalRegularUsers,
            'totalVendors' => $totalVendors,
            'chartjs' => $chartjs
        ]);
    }
    public function getStatistics()
    {
        $orders = Order::selectRaw('MONTH(created_at) as month, total, status')
            ->whereYear('created_at', date('Y'))
            ->get();

        $pending = [];
        $completed = [];
        $cancelled = [];
        $categories = [];

        // Initialize an array to store data for each month
        $monthsData = array_fill(1, 12, null);

        foreach ($orders as $order) {
            $month = (int)$order->month;
            $total = $order->total;
            $status = $order->status;

            // Assign the total to the corresponding month and status in the array
            if ($status == 'pending') {
                $pending[$month] = $total;
            } elseif ($status == 'completed') {
                $completed[$month] = $total;
            } elseif ($status == 'cancelled') {
                $cancelled[$month] = $total;
            }

            // We store all months' data to ensure that all categories are covered
            $monthsData[$month] = $total;
        }

        // Populate the data arrays and missing categories
        for ($month = 1; $month <= 12; $month++) {
            $categories[] = date('M', mktime(0, 0, 0, $month, 1)); // Get month abbreviation (Jan, Feb, etc.)

            // If there is no data for a specific status in a month, set it to 0
            $pendingg[] = $pending[$month] ?? 0;
            $completedd[] = $completed[$month] ?? 0;
            $cancelledd[] = $cancelled[$month] ?? 0;
        }

        $data = [
            'pendingg' => $pendingg,
            'completedd' => $completedd,
            'cancelledd' => $cancelledd,
            'categories' => $categories,
        ];

        return response()->json($data)->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
