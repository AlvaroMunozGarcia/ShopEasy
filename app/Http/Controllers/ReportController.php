<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB; // <-- AÑADIR ESTA LÍNEA

class ReportController extends Controller
{
    public function reports_day()
    {
        $sales=Sale::whereDate('sale_date',Carbon::today('America/Lima'))->get();
        $total =$sales->sum('total');
        return view('admin.report.reports_day',compact('sales','total'));
    }
    public function reports_date()
    {
       // Definimos fechas por defecto (por ejemplo, hoy) para la carga inicial
       $fecha_ini = Carbon::today('America/Lima');
       $fecha_fin = Carbon::today('America/Lima');
       return view('admin.report.reports_date', compact('fecha_ini', 'fecha_fin'));
    }
    public function report_results(Request $request)
    {
        $request->validate([
            'fecha_ini' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_ini',
        ]);

        $fecha_ini = Carbon::parse($request->fecha_ini)->startOfDay(); 
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();  
        $sales = Sale::whereBetween('sale_date', [$fecha_ini, $fecha_fin])->get();
        $total = $sales->sum('total');
        return view('admin.report.reports_date', compact('sales', 'total', 'fecha_ini', 'fecha_fin'));
    }







    public function salesByCategoryForm()
    {
        return view('admin.report.sales_by_category');
    }

    // Método para procesar y mostrar los resultados del reporte de ventas por categoría
    public function salesByCategoryResults(Request $request)
    {
        $request->validate([
            'fecha_ini' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_ini',
        ]);

        $fecha_ini = Carbon::parse($request->fecha_ini)->startOfDay();
        $fecha_fin = Carbon::parse($request->fecha_fin)->endOfDay();

        $salesByCategory = SaleDetail::join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.status', 'VALID')
            ->whereBetween('sales.sale_date', [$fecha_ini, $fecha_fin])
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_details.quantity * sale_details.price) as total_amount_sold'),
                DB::raw('SUM(sale_details.quantity) as total_quantity_sold')
            )
            ->groupBy('categories.name')
            ->orderByDesc('total_amount_sold')
            ->get();

        $totalGeneralAmount = $salesByCategory->sum('total_amount_sold');
        $totalGeneralQuantity = $salesByCategory->sum('total_quantity_sold');

        return view('admin.report.sales_by_category', [
            'salesByCategory' => $salesByCategory,
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'totalGeneralAmount' => $totalGeneralAmount,
            'totalGeneralQuantity' => $totalGeneralQuantity,
        ]);
    }





    
}
