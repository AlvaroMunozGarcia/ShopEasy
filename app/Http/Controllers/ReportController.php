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
        $sales=Sale::whereDate('sale_date',Carbon::today('America/Lima'))->get();
        $total =$sales->sum('total');
        return view('admin.report.reports_date',compact('sales','total'));

    }
    public function report_results(Request $request)
    {
        $fi =$request->fecha_ini.'00:00:00';
        $ff =$request->fecha_fin.'23:59:59';
        $sales=Sale::whereBetween('sale_date',$fi,$ff)->get();
        $total =$sales->sum('total');
        return redirect()->route('reports.date',compact('sales','total'));
    }
}
