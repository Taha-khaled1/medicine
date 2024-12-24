<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use DB;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{   function __construct()
    {
         $this->middleware('permission:الصفحه الرئيسيه للتاجر', ['only' => ['vendorMain']]);

    }
public function vendorMain()
{
    $merchant = User::findOrFail(Auth::user()->id);
    $merchantId = $merchant->id;
         $totalSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
    ->join('products', 'order_items.product_id', '=', 'products.id')
    ->where('orders.status', '!=', 'cancelled')
    ->where('products.user_id', $merchantId)
    ->sum('order_items.quantity');
    $totalRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('orders.status',"!=","orders.cancelled")
        ->where('products.user_id', $merchantId)
        ->sum(DB::raw('order_items.quantity * products.price'));
//    return $totalRevenue;
    $totalDiscountsGiven = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->where('orders.status', 'completed')
        ->where('products.user_id', $merchantId)
        ->sum(DB::raw('order_items.quantity * products.discount'));

    $totalProducts = Product::where('user_id', $merchantId)->count();
    $totalProductsActive = Product::where('user_id', $merchantId)->where('status', 1)->count();
    $totalProductsInActive = Product::where('user_id', $merchantId)->where('status', 0)->count();



    $topProductViews = Product::where('user_id', $merchantId)
        ->orderByDesc('views')
        ->take(5)
        ->get();

    $topSellingProducts = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->select('products.id', 'products.name_en', 'products.name_ar','products.price', 'products.description_en', 'products.user_id','products.description_ar', 'products.image', DB::raw('SUM(order_items.quantity) as total_sold'))
        ->where('products.user_id', $merchantId)
        ->groupBy('products.id', 'products.name_en', 'products.name_ar', 'products.price','products.description_en','products.user_id', 'products.description_ar', 'products.image')
        ->orderByDesc('total_sold')
        ->take(5)
        ->get();


    $data = [
        'totalRevenue' => $totalRevenue,
        'totalProducts' => $totalProducts,
        'totalProductsActive' => $totalProductsActive,
        'totalProductsInActive' => $totalProductsInActive,
        'totalDiscountsGiven' => $totalDiscountsGiven,
        'topProductViews' => $topProductViews,
        'topSellingProducts' => $topSellingProducts,
    ];

    // Return the JSON response with the calculated data
    // return response()->json($data);
        
    // Pass the data to the view using the with() method
    return view('dashboard.home.vendor-static', compact(
        'totalRevenue',
        'totalProducts',
        'totalProductsActive',
        'totalProductsInActive',
        'totalDiscountsGiven',
        'topProductViews',
        'topSellingProducts',    'totalSales',
    ));
}
}