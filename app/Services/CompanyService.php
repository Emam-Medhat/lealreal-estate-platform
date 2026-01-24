<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CompanyService
{
    /**
     * Create a new company
     */
    public function createCompany(array $data): Company
    {
        DB::beginTransaction();
        try {
            $company = Company::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'description' => $data['description'] ?? null,
                'logo_url' => $data['logo_url'] ?? null,
                'license_number' => $data['license_number'],
                'established_date' => $data['established_date'] ?? now(),
                'company_size' => $data['company_size'] ?? 'small',
                'industry' => $data['industry'] ?? 'real_estate',
                'address' => $data['address'],
                'city' => $data['city'],
                'country' => $data['country'],
                'postal_code' => $data['postal_code'] ?? null,
                'status' => 'active',
                'subscription_status' => 'active',
                'subscription_plan_id' => $data['subscription_plan_id'] ?? null,
                'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Create default settings
            $this->createDefaultSettings($company);

            // Create owner membership
            $company->members()->create([
                'user_id' => auth()->id(),
                'role' => 'owner',
                'status' => 'active',
                'joined_at' => now()
            ]);

            // Clear company cache
            Cache::tags(['company', 'company.' . $company->id])->flush();

            DB::commit();

            Log::info('Company created successfully', [
                'company_id' => $company->id,
                'name' => $company->name,
                'created_by' => auth()->id()
            ]);

            return $company->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create company', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update company information
     */
    public function updateCompany(int $companyId, array $data): Company
    {
        $company = Company::findOrFail($companyId);

        DB::beginTransaction();
        try {
            $oldData = $company->toArray();
            
            $company->update($data);

            // Log changes
            $this->logCompanyChanges($company, $oldData, $data);

            // Update cache
            Cache::tags(['company', 'company.' . $company->id])->flush();

            DB::commit();

            Log::info('Company updated successfully', [
                'company_id' => $companyId,
                'updated_by' => auth()->id()
            ]);

            return $company->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update company', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Add member to company
     */
    public function addMember(int $companyId, int $userId, string $role = 'member'): CompanyMember
    {
        $company = Company::findOrFail($companyId);

        // Check if user is already a member
        $existingMember = $company->members()
            ->where('user_id', $userId)
            ->first();

        if ($existingMember) {
            throw new \Exception('User is already a member of this company');
        }

        DB::beginTransaction();
        try {
            $member = $company->members()->create([
                'user_id' => $userId,
                'role' => $role,
                'status' => 'active',
                'joined_at' => now(),
                'added_by' => auth()->id()
            ]);

            // Update member count
            $this->updateMemberCount($company);

            // Clear cache
            Cache::tags(['company', 'company.' . $companyId])->flush();

            DB::commit();

            Log::info('Member added to company', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'role' => $role,
                'added_by' => auth()->id()
            ]);

            return $member;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add member to company', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Remove member from company
     */
    public function removeMember(int $companyId, int $userId): bool
    {
        $company = Company::findOrFail($companyId);
        $member = $company->members()
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            throw new \Exception('User is not a member of this company');
        }

        // Don't allow removing owner or last manager
        if ($member->role === 'owner' || $this->isLastManager($company, $member)) {
            throw new \Exception('Cannot remove this member from company');
        }

        DB::beginTransaction();
        try {
            $member->delete();

            // Update member count
            $this->updateMemberCount($company);

            // Clear cache
            Cache::tags(['company', 'company.' . $companyId])->flush();

            DB::commit();

            Log::info('Member removed from company', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'removed_by' => auth()->id()
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove member from company', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update member role
     */
    public function updateMemberRole(int $companyId, int $userId, string $role): bool
    {
        $company = Company::findOrFail($companyId);
        $member = $company->members()
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            throw new \Exception('User is not a member of this company');
        }

        // Don't allow changing owner role
        if ($member->role === 'owner' && $role !== 'owner') {
            throw new \Exception('Cannot change owner role');
        }

        DB::beginTransaction();
        try {
            $member->update(['role' => $role]);

            // Clear cache
            Cache::tags(['company', 'company.' . $companyId])->flush();

            DB::commit();

            Log::info('Member role updated', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'old_role' => $member->role,
                'new_role' => $role,
                'updated_by' => auth()->id()
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update member role', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create branch for company
     */
    public function createBranch(int $companyId, array $data): CompanyBranch
    {
        $company = Company::findOrFail($companyId);

        DB::beginTransaction();
        try {
            $branch = $company->branches()->create([
                'name' => $data['name'],
                'address' => $data['address'],
                'city' => $data['city'],
                'country' => $data['country'],
                'postal_code' => $data['postal_code'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'manager_id' => $data['manager_id'] ?? null,
                'status' => 'active',
                'established_date' => $data['established_date'] ?? now(),
                'created_by' => auth()->id()
            ]);

            // Update branch count
            $this->updateBranchCount($company);

            // Clear cache
            Cache::tags(['company', 'company.' . $companyId])->flush();

            DB::commit();

            Log::info('Branch created for company', [
                'company_id' => $companyId,
                'branch_name' => $data['name'],
                'created_by' => auth()->id()
            ]);

            return $branch;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create branch', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Get company statistics
     */
    public function getCompanyStatistics(int $companyId): array
    {
        $company = Company::findOrFail($companyId);

        $memberCount = $company->members()->count();
        $branchCount = $company->branches()->count();
        $propertyCount = Property::where('company_id', $companyId)->count();

        return [
            'member_count' => $memberCount,
            'branch_count' => $branchCount,
            'property_count' => $propertyCount,
            'subscription_status' => $company->subscription_status,
            'subscription_plan' => $company->subscription_plan_id ? $company->subscriptionPlan->name : null,
            'subscription_expires_at' => $company->subscription_expires_at,
            'created_at' => $company->created_at->toDateString(),
            'updated_at' => $company->updated_at->toDateString()
        ];
    }

    /**
     * Update member count in company
     */
    private function updateMemberCount(Company $company): void
    {
        $memberCount = $company->members()->count();
        $company->member_count = $memberCount;
        $company->save();
    }

    /**
     * Update branch count in company
     */
    private function updateBranchCount(Company $company): void
    {
        $branchCount = $company->branches()->count();
        $company->branch_count = $branchCount;
        $company->save();
    }

    /**
     * Log company changes
     */
    private function logCompanyChanges(Company $company, array $oldData, array $newData): void
    {
        $changes = [];
        
        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[] = [
                    'field' => $key,
                    'old_value' => $oldData[$key] ?? null,
                    'new_value' => $value,
                    'changed_at' => now()
                ];
            }
        }

        if (!empty($changes)) {
            $company->activities()->create([
                'activity_type' => 'company_update',
                'action' => 'company_updated',
                'data' => [
                    'changes' => $changes,
                    'updated_by' => auth()->id()
                ],
                'created_at' => now()
            ]);
        }
    }

    /**
     * Check if user is the last manager in company
     */
    private function isLastManager(Company $company, CompanyMember $member): bool
    {
        $managers = $company->members()
            ->where('role', 'manager')
            ->orderBy('created_at', 'desc')
            ->get();

        return $managers->isEmpty() || $managers->first()->id === $member->id;
    }

    /**
     * Create default company settings
     */
    private function createDefaultSettings(Company $company): void
    {
        $company->settings()->createMany([
            [
                'key' => 'notifications',
                'value' => json_encode([
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'push_notifications' => true,
                    'property_alerts' => true,
                    'member_alerts' => true,
                    'report_alerts' => true
                ])
            ],
            [
                'key' => 'privacy',
                'value' => json_encode([
                    'public_profile' => false,
                    'public_properties' => true,
                    'public_analytics' => false,
                    'allow_member_invites' => true
                ])
            ],
            [
                'key' => 'features',
                'value' => json_encode([
                    'property_management' => true,
                    'member_management' => true,
                    'branch_management' => true,
                    'analytics' => true,
                    'reports' => true,
                    'document_management' => true,
                    'api_access' => false
                ])
            ],
            [
                'key' => 'branding',
                'value' => json_encode([
                    'primary_color' => '#007bff',
                    'secondary_color' => '#6c757d',
                    'logo_url' => null,
                    'custom_css' => null
                ])
            ]
        ]);
    }
}
