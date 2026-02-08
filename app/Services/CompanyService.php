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

use App\Repositories\Contracts\CompanyRepositoryInterface;

class CompanyService
{
    protected $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Get paginated companies
     */
    public function getPaginatedCompanies(array $filters, int $perPage = 20)
    {
        return $this->companyRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get active companies
     */
    public function getActiveCompanies(array $filters = [])
    {
        return $this->companyRepository->getActiveCompanies($filters);
    }

    /**
     * Get company by ID
     */
    public function getCompanyById(int $id): Company
    {
        return $this->companyRepository->findById($id);
    }

    /**
     * Delete company
     */
    public function deleteCompany(int $id): bool
    {
        $company = $this->companyRepository->findById($id);

        DB::beginTransaction();
        try {
            // Delete related data logic if needed
            // For now, simple delete
            $this->companyRepository->delete($id);

            // Clear cache
            Cache::tags(['company', 'company.' . $id])->flush();

            DB::commit();

            Log::info('Company deleted successfully', [
                'company_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete company', [
                'company_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new company
     */
    public function createCompany(array $data): Company
    {
        DB::beginTransaction();
        try {
            $companyData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'type' => $data['type'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'api_key' => $data['api_key'] ?? null,
                'webhook_url' => $data['webhook_url'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'is_verified' => $data['is_verified'] ?? false,
                'verification_level' => $data['verification_level'] ?? 0,
                'rating' => $data['rating'] ?? 0,
                'total_reviews' => $data['total_reviews'] ?? 0,
                'cover_image_url' => $data['cover_image_url'] ?? null,
                'logo_url' => $data['logo_url'] ?? null,
                'created_by' => auth()->id()
            ];
            
            $company = $this->companyRepository->create($companyData);

            // Create profile
            $profileData = [
                'description' => $data['description'] ?? null,
                'founded_date' => $data['founded_date'] ?? null,
                'employee_count' => $data['employee_count'] ?? null,
                'annual_revenue' => $data['annual_revenue'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'services' => $data['services'] ?? null,
                'specializations' => $data['specializations'] ?? null,
                'certifications' => $data['certifications'] ?? null,
                'awards' => $data['awards'] ?? null,
                'logo' => $data['logo'] ?? null,
            ];
            
            $company->profile()->create($profileData);

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
        $company = $this->companyRepository->findById($companyId);

        DB::beginTransaction();
        try {
            $oldData = $company->toArray();
            
            // Update Company
            $companyData = array_intersect_key($data, array_flip([
                'name', 'email', 'phone', 'website', 'type', 
                'registration_number', 'tax_id', 'status',
                'api_key', 'webhook_url', 'is_featured', 'is_verified',
                'verification_level', 'rating', 'total_reviews',
                'cover_image_url', 'logo_url'
            ]));
            
            if (!empty($companyData)) {
                $this->companyRepository->update($company->id, $companyData);
            }

            // Update Profile
            $profileData = [
                'description' => $data['description'] ?? null,
                'founded_date' => $data['founded_date'] ?? null,
                'employee_count' => $data['employee_count'] ?? null,
                'annual_revenue' => $data['annual_revenue'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'services' => $data['services'] ?? null,
                'specializations' => $data['specializations'] ?? null,
                'certifications' => $data['certifications'] ?? null,
                'awards' => $data['awards'] ?? null,
                'logo' => $data['logo'] ?? null,
            ];
            
            // Filter out null values to avoid overwriting with null unless explicitly intended
            // But here we rely on the fact that if it's not in $data it's null in mapping.
            // If $data comes from request->all(), it might have everything.
            // Let's filter based on keys present in $data.
            $profileData = array_intersect_key($data, array_flip([
                'description', 'founded_date', 'employee_count', 'annual_revenue',
                'address', 'city', 'state', 'country', 'postal_code',
                'services', 'specializations', 'certifications', 'awards', 'logo'
            ]));

            if (!empty($profileData)) {
                $company->profile()->updateOrCreate(
                    ['company_id' => $company->id],
                    $profileData
                );
            }

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
        $company = $this->companyRepository->findById($companyId);

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
        $company = $this->companyRepository->findById($companyId);
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
        $company = $this->companyRepository->findById($companyId);
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
        $company = $this->companyRepository->findById($companyId);

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
        $company = $this->companyRepository->findById($companyId);

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
