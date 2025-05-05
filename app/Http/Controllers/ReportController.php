<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;

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
}
