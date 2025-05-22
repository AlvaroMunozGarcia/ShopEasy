<?php

namespace App\Http\Controllers;

use App\Models\Client; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Product; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Provider; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Purchase; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Sale; // Asegúrate que la ruta al modelo sea correcta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Para cálculos más complejos si es necesario
use Carbon\Carbon; // Necesario para manejar fechas

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \Exception
     */
    public function index()
    {
        // Contadores básicos
        $totalSales = Sale::where('status', 'VALID')->count();
        $totalPurchases = Purchase::where('status', 'VALID')->count();
        $totalClients = Client::count();
        $totalProviders = Provider::count();
        $totalProducts = Product::count();

        // Sumas
        $totalRevenue = Sale::where('status', 'VALID')->sum('total');
        $totalExpenditure = Purchase::where('status', 'VALID')->sum('total');

        // --- Datos para Gráficos ---
        // Ventas de los últimos 7 días
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

        // Comparativa mensual Ventas vs Compras (últimos 12 meses)
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

        // Productos más vendidos (ejemplo: top 5 por cantidad total vendida)
        // Esta consulta es un ejemplo y puede necesitar ajustes según tu estructura y lógica de negocio.
        // Considera el rendimiento si tienes muchos datos.
        $productosMasVendidos = Product::select('products.name', DB::raw('SUM(sale_details.quantity) as total_quantity_sold'))
            ->join('sale_details', 'products.id', '=', 'sale_details.product_id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'VALID') // Considera solo ventas válidas
            ->groupBy('products.id', 'products.name') // Agrupa por ID y nombre del producto
            ->orderByDesc('total_quantity_sold')
            ->take(5) // Top 5 productos
            ->get();

        // NUEVO: Obtener productos con stock bajo o agotados
        $lowStockProducts = Product::query()
            ->where(function ($query) {
                // Productos por debajo del mínimo (y el mínimo está definido > 0)
                $query->whereColumn('stock', '<=', 'min_stock')
                      ->where('min_stock', '>', 0);
            })
            ->orWhere('stock', '=', 0) // Productos agotados (stock es 0)
            ->orderByRaw('CASE WHEN stock = 0 THEN 0 ELSE 1 END') // Agotados primero
            ->orderBy('stock', 'asc') // Luego los de menor stock actual
            ->orderBy('name', 'asc')  // Alfabéticamente por nombre
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
