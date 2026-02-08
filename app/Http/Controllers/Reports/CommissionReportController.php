<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionReportController extends Controller
{
    public function index()
    {
        return view('reports.placeholder', [
            'title' => 'Commission Reports',
            'backRoute' => route('reports.index'),
            'backLabel' => 'Return to Reports'
        ]);
    }
}
