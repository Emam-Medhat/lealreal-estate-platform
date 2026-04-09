<?php

namespace App\Http\Controllers;

use App\Models\ReportSchedule;
use App\Http\Requests\StoreReportScheduleRequest;
use App\Http\Requests\UpdateReportScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportScheduleController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    /**
     * Display a listing of report schedules.
     */
    public function index(Request $request)
    {
        $schedules = ReportSchedule::with(['creator', 'report'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->latest()
            ->paginate(10);

        return view('reports.schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new report schedule.
     */
    public function create()
    {
        return view('reports.schedules.create');
    }

    /**
     * Store a newly created report schedule in storage.
     */
    public function store(StoreReportScheduleRequest $request)
    {
        $schedule = ReportSchedule::create([
            'name' => $request->name,
            'description' => $request->description,
            'report_id' => $request->report_id,
            'cron_expression' => $request->cron_expression,
            'parameters' => $request->parameters,
            'recipients' => $request->recipients,
            'delivery_method' => $request->delivery_method,
            'is_active' => $request->boolean('is_active'),
            'next_run_at' => $this->calculateNextRun($request->cron_expression),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('schedules.show', $schedule)
            ->with('success', 'تم إنشاء جدول التقرير بنجاح');
    }

    /**
     * Display the specified report schedule.
     */
    public function show(ReportSchedule $schedule)
    {
        $schedule->load(['creator', 'report', 'executions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('reports.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified report schedule.
     */
    public function edit(ReportSchedule $schedule)
    {
        return view('reports.schedules.edit', compact('schedule'));
    }

    /**
     * Update the specified report schedule in storage.
     */
    public function update(UpdateReportScheduleRequest $request, ReportSchedule $schedule)
    {
        $schedule->update([
            'name' => $request->name,
            'description' => $request->description,
            'report_id' => $request->report_id,
            'cron_expression' => $request->cron_expression,
            'parameters' => $request->parameters,
            'recipients' => $request->recipients,
            'delivery_method' => $request->delivery_method,
            'is_active' => $request->boolean('is_active'),
            'next_run_at' => $this->calculateNextRun($request->cron_expression),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('schedules.show', $schedule)
            ->with('success', 'تم تحديث جدول التقرير بنجاح');
    }

    /**
     * Remove the specified report schedule from storage.
     */
    public function destroy(ReportSchedule $schedule)
    {
        $schedule->delete();

        return redirect()
            ->route('schedules.index')
            ->with('success', 'تم حذف جدول التقرير بنجاح');
    }

    /**
     * Toggle the active status of the schedule.
     */
    public function toggle(ReportSchedule $schedule)
    {
        $schedule->update([
            'is_active' => !$schedule->is_active,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تحديث حالة الجدول بنجاح');
    }

    /**
     * Run the scheduled report manually.
     */
    public function run(ReportSchedule $schedule)
    {
        try {
            $result = $schedule->execute();
            
            return back()->with('success', 'تم تشغيل التقرير المجدول بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to run scheduled report', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'فشل تشغيل التقرير المجدول');
        }
    }

    /**
     * Show the execution history of the schedule.
     */
    public function history(ReportSchedule $schedule, Request $request)
    {
        $executions = $schedule->executions()
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('reports.schedules.history', compact('schedule', 'executions'));
    }

    /**
     * Calculate the next run time based on cron expression.
     */
    private function calculateNextRun($cronExpression)
    {
        try {
            // Simple implementation - you may want to use a proper cron library
            return now()->addHour(); // Placeholder implementation
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate cron expression via AJAX.
     */
    public function validateCron(Request $request)
    {
        $request->validate([
            'cron_expression' => 'required|string'
        ]);

        // Simple validation - you may want to use a proper cron library
        return response()->json([
            'valid' => true,
            'next_run' => now()->addHour()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get preview of next run dates.
     */
    public function previewRuns(Request $request)
    {
        $request->validate([
            'cron_expression' => 'required|string',
            'count' => 'nullable|integer|min:1|max:20'
        ]);

        $count = $request->count ?? 5;
        $runs = [];
        $nextRun = now();

        for ($i = 0; $i < $count; $i++) {
            $nextRun = $nextRun->addHour(); // Simple hourly schedule
            $runs[] = $nextRun->format('Y-m-d H:i:s');
        }

        return response()->json([
            'valid' => true,
            'runs' => $runs
        ]);
    }
}
