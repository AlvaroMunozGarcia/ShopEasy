<?php

namespace App\Http\Controllers;

use App\Models\Client; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Product; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Provider; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Purchase; // Asegúrate que la ruta al modelo sea correcta
use App\Models\Sale; // Asegúrate que la ruta al modelo sea correcta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Para cálculos más complejos si es necesario

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

        // Puedes añadir más métricas: ventas de hoy, productos con bajo stock, etc.

        // Asegúrate de que la vista se llame 'admin.home' o ajusta el nombre según tu estructura
        return view('admin.home', compact(
            'totalSales',
            'totalPurchases',
            'totalClients',
            'totalProviders',
            'totalProducts',
            'totalRevenue',
            'totalExpenditure'
        ));
    }
}
