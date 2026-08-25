<?php

namespace App\Services;

use App\Repositories\Contracts\ServiceProviderRepositoryInterface;
use App\Models\ServiceProvider;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class ServiceProviderService
{
    protected $providerRepository;

    public function __construct(ServiceProviderRepositoryInterface $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function getAllProviders(array $filters, int $perPage = 15)
    {
        return $this->providerRepository->getPaginated($filters, $perPage);
    }

    public function getProviderDetails(int $id)
    {
        return $this->providerRepository->findWithDetails($id);
    }

    public function createProvider(array $data, array $attachments = [])
    {
        return DB::transaction(function () use ($data, $attachments) {
            $data['provider_code'] = 'PROV-' . date('Y') . '-' . str_pad(ServiceProvider::count() + 1, 4, '0', STR_PAD_LEFT);
            $data['specializations'] = json_encode($data['specializations'] ?? []);
            $data['is_active'] = $data['is_active'] ?? true;

            $provider = $this->providerRepository->create($data);

            if (!empty($attachments)) {
                foreach ($attachments as $file) {
                    $path = $file->store('provider_attachments', 'public');
                    // Log attachment or create record if needed
                }
            }

            $this->logActivity('created_provider', "Created service provider: {$provider->name}");

            return $provider;
        });
    }

    public function updateProvider(ServiceProvider $provider, array $data)
    {
        return DB::transaction(function () use ($provider, $data) {
            if (isset($data['specializations'])) {
                $data['specializations'] = json_encode($data['specializations']);
            }

            $provider->update($data);

            $this->logActivity('updated_provider', "Updated service provider: {$provider->name}");

            return $provider;
        });
    }

    public function deleteProvider(ServiceProvider $provider)
    {
        if ($provider->maintenanceRequests()->where('status', '!=', 'completed')->exists()) {
            throw new \Exception('Cannot delete provider with active maintenance requests.');
        }

        return DB::transaction(function () use ($provider) {
            $name = $provider->name;
            $provider->delete();
            $this->logActivity('deleted_provider', "Deleted service provider: {$name}");
            return true;
        });
    }

    public function toggleProviderStatus(ServiceProvider $provider)
    {
        $provider->update(['is_active' => !$provider->is_active]);
        $status = $provider->is_active ? 'activated' : 'deactivated';
        $this->logActivity('toggled_provider_status', "Toggled service provider {$provider->name} status to {$status}");
        return $provider->is_active;
    }

    public function updateProviderRating(ServiceProvider $provider, float $rating, ?string $review = null)
    {
        return $provider->update([
            'rating' => $rating,
            'last_review' => $review,
            'last_review_date' => now(),
        ]);
    }

    public function getAvailableProviders(string $serviceType, string $date, int $duration = 60)
    {
        return $this->providerRepository->getAvailableProviders($serviceType, $date, $duration);
    }

    public function getProviderPerformance(ServiceProvider $provider)
    {
        // Check DB driver to support both MySQL and SQLite for time differences & date extractions
        $driver = DB::getDriverName();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        if ($driver === 'sqlite') {
            $avgTimeExpr = "AVG(CASE WHEN status = 'completed' THEN (julianday(completed_at) - julianday(started_at)) * 24 END)";
            $monthCond = "CAST(strftime('%m', created_at) AS INTEGER) = {$currentMonth} AND CAST(strftime('%Y', created_at) AS INTEGER) = {$currentYear}";
            $completedMonthCond = "CAST(strftime('%m', completed_at) AS INTEGER) = {$currentMonth} AND CAST(strftime('%Y', completed_at) AS INTEGER) = {$currentYear}";
            $monthlySelect = "CAST(strftime('%m', completed_at) AS INTEGER) as month, CAST(strftime('%Y', completed_at) AS INTEGER) as year, COUNT(*) as count, SUM(actual_cost) as revenue";
        } else {
            $avgTimeExpr = "AVG(CASE WHEN status = 'completed' THEN TIMESTAMPDIFF(HOUR, started_at, completed_at) END)";
            $monthCond = "MONTH(created_at) = {$currentMonth} AND YEAR(created_at) = {$currentYear}";
            $completedMonthCond = "MONTH(completed_at) = {$currentMonth} AND YEAR(completed_at) = {$currentYear}";
            $monthlySelect = 'MONTH(completed_at) as month, YEAR(completed_at) as year, COUNT(*) as count, SUM(actual_cost) as revenue';
        }

        // Consolidated stats query: 1 query instead of 7
        $rawStats = $provider->maintenanceRequests()
            ->selectRaw("
                COUNT(*) as total_requests,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as total_completed,
                COUNT(CASE WHEN {$monthCond} THEN 1 END) as monthly_requests,
                COUNT(CASE WHEN status = 'completed' AND {$completedMonthCond} THEN 1 END) as monthly_completed,
                SUM(CASE WHEN status = 'completed' AND {$completedMonthCond} THEN actual_cost ELSE 0 END) as monthly_revenue,
                {$avgTimeExpr} as average_completion_time
            ")
            ->first();

        $totalCompleted = (int) ($rawStats->total_completed ?? 0);
        $totalRequests = (int) ($rawStats->total_requests ?? 0);

        $stats = [
            'monthly_requests' => (int) ($rawStats->monthly_requests ?? 0),
            'monthly_completed' => (int) ($rawStats->monthly_completed ?? 0),
            'monthly_revenue' => (float) ($rawStats->monthly_revenue ?? 0),
            'average_completion_time' => $rawStats->average_completion_time !== null ? (float) $rawStats->average_completion_time : null,
            'completion_rate' => $totalCompleted > 0 ? ($totalCompleted / $totalRequests) * 100 : 0,
        ];

        // Monthly trends query
        $monthlyData = $provider->maintenanceRequests()
            ->where('status', 'completed')
            ->selectRaw($monthlySelect)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return compact('stats', 'monthlyData');
    }

    protected function logActivity(string $action, string $details)
    {
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }
}
