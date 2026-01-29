<?php

namespace App\Observers;

use App\Models\Agent;
use App\Models\AgentReview;
use App\Models\AgentPerformance;
use Illuminate\Support\Facades\Log;

class AgentObserver
{
    /**
     * Handle the Agent "created" event.
     */
    public function created(Agent $agent): void
    {
        try {
            // Create initial performance record
            $this->createInitialPerformance($agent);
            
            // Send welcome notification
            $this->sendWelcomeNotification($agent);

            // Send notification to admins and managers
            try {
                $admins = \App\Models\User::whereIn('role', ['admin', 'manager'])->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AgentCreated($agent));
            } catch (\Exception $e) {
                \Log::warning('Could not send agent creation notification: ' . $e->getMessage());
            }
            
            Log::info('Agent created with initial setup', [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to setup agent after creation', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Agent "updated" event.
     */
    public function updated(Agent $agent): void
    {
        try {
            // Update performance metrics
            $this->updatePerformanceMetrics($agent);
            
            // Log changes
            $this->logAgentChanges($agent);
            
            Log::info('Agent updated', [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle agent update', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Agent "deleted" event.
     */
    public function deleted(Agent $agent): void
    {
        try {
            // Archive agent data
            $this->archiveAgentData($agent);
            
            // Cancel active commissions
            $this->cancelActiveCommissions($agent);
            
            Log::info('Agent deleted', [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle agent deletion', [
                'agent_id' => $agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create initial performance record
     */
    private function createInitialPerformance(Agent $agent): void
    {
        $agent->performanceMetrics()->create([
            'period' => 'initial',
            'year' => now()->year,
            'month' => now()->month,
            'performance_score' => 0,
            'sales_count' => 0,
            'sales_revenue' => 0,
            'leads_count' => 0,
            'converted_leads' => 0,
            'appointments_count' => 0,
            'completed_appointments' => 0,
            'commissions_amount' => 0,
            'calculated_at' => now()
        ]);
    }

    /**
     * Update performance metrics
     */
    private function updatePerformanceMetrics(Agent $agent): void
    {
        // Calculate current month performance
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $totalSales = $agent->sales()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $totalRevenue = $agent->sales()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('sale_price');

        $totalLeads = $agent->leads()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $convertedLeads = $agent->leads()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'converted')
            ->count();

        $totalAppointments = $agent->appointments()
            ->whereMonth('start_time', $currentMonth)
            ->whereYear('start_time', $currentYear)
            ->count();

        $completedAppointments = $agent->appointments()
            ->whereMonth('start_time', $currentMonth)
            ->whereYear('start_time', $currentYear)
            ->where('status', 'completed')
            ->count();

        $totalCommissions = $agent->commissions()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'paid')
            ->sum('amount');

        // Calculate performance score
        $performanceScore = $this->calculatePerformanceScore($agent, $currentMonth, $currentYear);

        // Update or create performance record
        $performanceRecord = $agent->performanceMetrics()
            ->where('period', 'monthly')
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        if ($performanceRecord) {
            $performanceRecord->update([
                'performance_score' => $performanceScore,
                'sales_count' => $totalSales,
                'sales_revenue' => $totalRevenue,
                'leads_count' => $totalLeads,
                'converted_leads' => $convertedLeads,
                'appointments_count' => $totalAppointments,
                'completed_appointments' => $completedAppointments,
                'commissions_amount' => $totalCommissions,
                'calculated_at' => now()
            ]);
        } else {
            $agent->performanceMetrics()->create([
                'period' => 'monthly',
                'year' => $currentYear,
                'month' => $currentMonth,
                'performance_score' => $performanceScore,
                'sales_count' => $totalSales,
                'sales_revenue' => $totalRevenue,
                'leads_count' => $totalLeads,
                'converted_leads' => $convertedLeads,
                'appointments_count' => $totalAppointments,
                'completed_appointments' => $completedAppointments,
                'commissions_amount' => $totalCommissions,
                'calculated_at' => now()
            ]);
        }
    }

    /**
     * Log agent changes
     */
    private function logAgentChanges(Agent $agent): void
    {
        $changes = $agent->getDirty();
        
        if (!empty($changes)) {
            $agent->activities()->create([
                'activity_type' => 'agent_update',
                'action' => 'agent_updated',
                'data' => [
                    'changes' => $changes,
                    'updated_by' => auth()->id()
                ],
                'created_at' => now()
            ]);
        }
    }

    /**
     * Archive agent data
     */
    private function archiveAgentData(Agent $agent): void
    {
        // This would integrate with your archiving system
        // Placeholder implementation
        
        Log::info('Agent data archived', [
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'archived_at' => now()
        ]);
    }

    /**
     * Cancel active commissions
     */
    private function cancelActiveCommissions(Agent $agent): void
    {
        $activeCommissions = $agent->commissions()
            ->where('status', 'pending')
            ->get();

        foreach ($activeCommissions as $commission) {
            $commission->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'agent_deleted'
            ]);
        }
        
        Log::info('Active commissions cancelled', [
            'agent_id' => $agent->id,
            'cancelled_count' => $activeCommissions->count()
        ]);
    }

    /**
     * Calculate performance score
     */
    private function calculatePerformanceScore(Agent $agent, string $month, int $year): int
    {
        // Get metrics for the month
        $totalSales = $agent->sales()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $totalRevenue = $agent->sales()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('sale_price');

        $totalLeads = $agent->leads()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $convertedLeads = $agent->leads()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'converted')
            ->count();

        $totalAppointments = $agent->appointments()
            ->whereMonth('start_time', $month)
            ->whereYear('start_time', $year)
            ->count();

        $completedAppointments = $agent->appointments()
            ->whereMonth('start_time', $month)
            ->whereYear('start_time', $year)
            ->where('status', 'completed')
            ->count();

        $totalCommissions = $agent->commissions()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->sum('amount');

        // Calculate score based on multiple factors
        $score = 0;
        
        // Sales performance (40%)
        if ($totalSales >= 10) {
            $score += 15;
        } elseif ($totalSales >= 5) {
            $score += 10;
        } elseif ($totalSales >= 1) {
            $score += 5;
        }
        
        // Total revenue (15%)
        if ($totalRevenue >= 100000) {
            $score += 15;
        } elseif ($totalRevenue >= 50000) {
            $score += 10;
        } elseif ($totalRevenue >= 10000) {
            $score += 5;
        }
        
        // Conversion rate (20%)
        if ($totalLeads > 0) {
            $conversionRate = ($convertedLeads / $totalLeads) * 100;
            if ($conversionRate >= 25) {
                $score += 20;
            } elseif ($conversionRate >= 20) {
                $score += 15;
            } elseif ($conversionRate >= 15) {
                $score += 10;
            } elseif ($conversionRate >= 10) {
                $score += 5;
            }
        }
        
        // Appointment performance (20%)
        if ($totalAppointments > 0) {
            $completionRate = ($completedAppointments / $totalAppointments) * 100;
            if ($completionRate >= 90) {
                $score += 20;
            } elseif ($completionRate >= 80) {
                $score += 15;
            } elseif ($completionRate >= 70) {
                $score += 10;
            } elseif ($completionRate >= 50) {
                $score += 5;
            }
        }
        
        // Commission performance (10%)
        if ($totalCommissions >= 10000) {
            $score += 10;
        } elseif ($totalCommissions >= 5000) {
            $score += 7;
        } elseif ($totalCommissions >= 1000) {
            $score += 5;
        }
        
        return min($score, 100);
    }

    /**
     * Send welcome notification
     */
    private function sendWelcomeNotification(Agent $agent): void
    {
        $agent->notifications()->create([
            'title' => 'مرحباً بك في منصتنا',
            'message' => "تم تسجيلك كوكلاء في منصتنا. نحن سعداء بانضمامك لفريقنا.",
            'type' => 'agent_registered',
            'data' => [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'license_number' => $agent->license_number,
                'company_id' => $agent->company_id,
                'company_name' => $agent->company ? $agent->company->name : null,
                'registration_date' => $agent->created_at,
                'next_steps' => [
                    'complete_profile' => 'أكمل ملفك الشخصي',
                    'verify_license' => 'تحقق من رخصتك',
                    'add_portfolio' => 'أضف معرض أعمالك',
                    'attend_training' => 'حضور التدريب'
                ]
            ]
        ]);
    }
}
