<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reports_day()
    {
        return view('admin.report.day');
    }
    public function reports_date()
    {
        return view('admin.report.date');
    }
    public function report_results()
    {
        return view('admin.report.results');
    }
}
