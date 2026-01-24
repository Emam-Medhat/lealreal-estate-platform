<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SyncCompanyData implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $companyId;
    protected $syncType;

    /**
     * Create a new job instance.
     */
    public function __construct(int $companyId, string $syncType = 'full')
    {
        $this->companyId = $companyId;
        $this->syncType = $syncType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $company = Company::with(['members', 'branches', 'properties'])->find($this->companyId);
            
            if (!$company) {
                Log::error('Company not found for data sync', ['company_id' => $this->companyId]);
                return;
            }

            // Sync data based on type
            switch ($this->syncType) {
                case 'members':
                    $this->syncMembers($company);
                    break;
                case 'properties':
                    $this->syncProperties($company);
                    break;
                case 'analytics':
                    $this->syncAnalytics($company);
                    break;
                case 'full':
                default:
                    $this->syncAllData($company);
                    break;
            }

            // Update last sync timestamp
            $company->update(['last_synced_at' => now()]);

            // Clear company cache
            Cache::tags(['company', 'company.' . $this->companyId])->flush();

            Log::info('Company data synced successfully', [
                'company_id' => $this->companyId,
                'sync_type' => $this->syncType
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync company data', [
                'company_id' => $this->companyId,
                'sync_type' => $this->syncType,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Sync members data
     */
    private function syncMembers(Company $company): void
    {
        $members = $company->members()->with('user')->get();
        
        foreach ($members as $member) {
            // Sync user data to external systems
            $this->syncUserData($member->user);
            
            // Update member cache
            Cache::tags(['company', 'member', 'member.' . $member->id])
                ->remember('member_data.' . $member->id, now()->addHours(24), function () use ($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'role' => $member->role,
                        'status' => $member->status,
                        'joined_at' => $member->created_at,
                        'user' => [
                            'name' => $member->user->name,
                            'email' => $member->user->email,
                            'phone' => $member->user->phone
                        ]
                    ];
                });
        }
    }

    /**
     * Sync properties data
     */
    private function syncProperties(Company $company): void
    {
        $properties = $company->properties()->get();
        
        foreach ($properties as $property) {
            // Sync property data to external systems
            $this->syncPropertyData($property);
            
            // Update property cache
            Cache::tags(['company', 'property', 'property.' . $property->id])
                ->remember('property_data.' . $property->id, now()->addHours(24), function () use ($property) {
                    return [
                        'id' => $property->id,
                        'title' => $property->title,
                        'price' => $property->price,
                        'status' => $property->status,
                        'type' => $property->type,
                        'created_at' => $property->created_at
                    ];
                });
        }
    }

    /**
     * Sync analytics data
     */
    private function syncAnalytics(Company $company): void
    {
        // Sync analytics to external systems
        $analyticsData = [
            'total_properties' => $company->properties()->count(),
            'total_members' => $company->members()->count(),
            'active_members' => $company->members()->where('status', 'active')->count(),
            'total_leads' => $company->leads()->count(),
            'converted_leads' => $company->leads()->where('status', 'converted')->count(),
            'total_revenue' => $company->properties()->where('status', 'sold')->sum('price'),
            'last_updated' => now()
        ];

        // Update analytics cache
        Cache::tags(['company', 'analytics', 'company.' . $company->id])
            ->remember('analytics_data.' . $company->id, now()->addHours(1), function () use ($analyticsData) {
                return $analyticsData;
            });
    }

    /**
     * Sync all company data
     */
    private function syncAllData(Company $company): void
    {
        $this->syncMembers($company);
        $this->syncProperties($company);
        $this->syncAnalytics($company);
        
        // Sync branches data
        $branches = $company->branches()->get();
        
        foreach ($branches as $branch) {
            Cache::tags(['company', 'branch', 'branch.' . $branch->id])
                ->remember('branch_data.' . $branch->id, now()->addHours(24), function () use ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' => $branch->address,
                        'city' => $branch->city,
                        'country' => $branch->country,
                        'status' => $branch->status
                    ];
                });
        }
    }

    /**
     * Sync user data to external systems
     */
    private function syncUserData(User $user): void
    {
        // This would integrate with external CRM systems
        // Placeholder implementation
        
        Log::info('User data synced', [
            'user_id' => $user->id,
            'sync_type' => 'external_crm'
        ]);
    }

    /**
     * Sync property data to external systems
     */
    private function syncPropertyData($property): void
    {
        // This would integrate with external MLS systems
        // Placeholder implementation
        
        Log::info('Property data synced', [
            'property_id' => $property->id,
            'sync_type' => 'external_mls'
        ]);
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Company data sync job failed', [
            'company_id' => $this->companyId,
            'sync_type' => $this->syncType,
            'error' => $exception->getMessage()
        ]);
    }
}
