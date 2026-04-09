<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportScheduleController extends Controller
{
    /**
     * Store a new report schedule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:financial,sales,performance,market',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly',
            'time' => 'required|date_format:H:i',
            'recipients' => 'required|string',
            'formats' => 'required|array',
            'formats.*' => 'in:pdf,excel',
        ]);

        // In a real application, you would save this to the database
        // For now, we'll just redirect with a success message
        
        return redirect()
            ->route('reports.schedules.index')
            ->with('success', 'تم إنشاء جدولة التقرير بنجاح');
    }
}
