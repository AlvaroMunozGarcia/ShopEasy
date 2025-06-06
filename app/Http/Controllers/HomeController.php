<?php

namespace App\Http\Controllers;

use App\Models\Client; 
use App\Models\Product; 
use App\Models\Provider; 
use App\Models\Purchase; 
use App\Models\Sale; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \Exception
     */
    public function index()
    {
        $totalSales = Sale::where('status', 'VALID')->count();
        $totalPurchases = Purchase::where('status', 'VALID')->count();
        $totalClients = Client::count();
        $totalProviders = Provider::count();
        $totalProducts = Product::count();
        $totalRevenue = Sale::where('status', 'VALID')->sum('total');
        $totalExpenditure = Purchase::where('status', 'VALID')->sum('total');
        $salesLast7DaysLabels = [];
        $salesLast7DaysData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('America/Lima')->subDays($i);
            $salesLast7DaysLabels[] = $date->format('d M');
            $sales = Sale::where('status', 'VALID')
                         ->whereDate('sale_date', $date)
                         ->sum('total');
            $salesLast7DaysData[] = $sales;
        }
        $monthlyComparisonLabels = [];
        $monthlySalesData = [];
        $monthlyPurchasesData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::today('America/Lima')->subMonths($i);
            $year = $month->year;
            $monthNum = $month->month;
            $monthlyComparisonLabels[] = $month->format('M Y');

            $sales = Sale::where('status', 'VALID')
                         ->whereYear('sale_date', $year)
                         ->whereMonth('sale_date', $monthNum)
                         ->sum('total');
            $monthlySalesData[] = $sales;

            $purchases = Purchase::where('status', 'VALID')
                               ->whereYear('purchase_date', $year)
                               ->whereMonth('purchase_date', $monthNum)
                               ->sum('total');
            $monthlyPurchasesData[] = $purchases;
        }
        $productosMasVendidos = Product::select('products.name', DB::raw('SUM(sale_details.quantity) as total_quantity_sold'))
            ->join('sale_details', 'products.id', '=', 'sale_details.product_id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'VALID') 
            ->groupBy('products.id', 'products.name') 
            ->orderByDesc('total_quantity_sold')
            ->take(5) 
            ->get();
        $lowStockProducts = Product::query()
            ->where(function ($query) {
                $query->whereColumn('stock', '<=', 'min_stock')
                      ->where('min_stock', '>', 0);
            })
            ->orWhere('stock', '=', 0) 
            ->orderByRaw('CASE WHEN stock = 0 THEN 0 ELSE 1 END') 
            ->orderBy('stock', 'asc') 
            ->orderBy('name', 'asc')  
            ->get();

        return view('admin.home', compact(
            'totalSales',
            'totalPurchases',
            'totalClients',
            'totalProviders',
            'totalProducts',
            'totalRevenue',
            'totalExpenditure',
            'salesLast7DaysLabels',
            'salesLast7DaysData',
            'monthlyComparisonLabels',
            'monthlySalesData',
            'monthlyPurchasesData',
            'productosMasVendidos',
            'lowStockProducts' 
        ));
    }
}
