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
        $totalSales = Sale::where('status', 'VALID')->count(); // O como definas una venta válida
        $totalPurchases = Purchase::where('status', 'VALID')->count(); // O como definas una compra válida
        $totalClients = Client::count();
        $totalProviders = Provider::count();
        $totalProducts = Product::count();

        // Sumas (ejemplo: total vendido y comprado) - Ajusta el símbolo de moneda en la vista
        $totalRevenue = Sale::where('status', 'VALID')->sum('total');
        $totalExpenditure = Purchase::where('status', 'VALID')->sum('total');

        // --- Datos para Gráficos ---

        // 1. Ventas de los últimos 7 días
        $salesLast7DaysLabels = [];
        $salesLast7DaysData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('America/Lima')->subDays($i);
            $salesLast7DaysLabels[] = $date->format('d M'); // Formato '25 Dic'
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
            $monthlyComparisonLabels[] = $month->format('M Y'); // Formato 'Dic 2023'

            // Ventas del mes
            $sales = Sale::where('status', 'VALID')
                         ->whereYear('sale_date', $year)
                         ->whereMonth('sale_date', $monthNum)
                         ->sum('total');
            $monthlySalesData[] = $sales;

            // Compras del mes
            $purchases = Purchase::where('status', 'VALID') // Asegúrate que Purchase tenga status
                               ->whereYear('purchase_date', $year)
                               ->whereMonth('purchase_date', $monthNum)
                               ->sum('total');
            $monthlyPurchasesData[] = $purchases;
        }

        // --- Fin Datos para Gráficos ---

        // Asegúrate de que la vista se llame 'admin.home' o ajusta el nombre según tu estructura
        return view('admin.home', compact(
            // Métricas existentes
            'totalSales',
            'totalPurchases',
            'totalClients',
            'totalProviders',
            'totalProducts',
            'totalRevenue',
            'totalExpenditure',
            // Datos para gráficos
            'salesLast7DaysLabels',
            'salesLast7DaysData',
            'monthlyComparisonLabels',
            'monthlySalesData',
            'monthlyPurchasesData'
        ));
    }
}
