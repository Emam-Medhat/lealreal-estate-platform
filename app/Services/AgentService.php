<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Commission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AgentService
{
    /**
     * Register a new agent
     */
    public function registerAgent(array $data): Agent
    {
        DB::beginTransaction();
        try {
            // Create user account for agent
            $user = $this->createUserAccount($data);

            // Create agent profile
            $agent = Agent::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'bio' => $data['bio'] ?? null,
                'license_number' => $data['license_number'] ?? $this->generateLicenseNumber(),
                'specializations' => $data['specializations'] ?? null,
                'experience_years' => $data['experience_years'] ?? 0,
                'languages' => $data['languages'] ?? null,
                'areas_of_expertise' => $data['areas_of_expertise'] ?? [],
                'achievements' => $data['achievements'] ?? [],
                'status' => 'active'
            ]);

            // Generate license number
            if (!$data['license_number']) {
                $agent->update(['license_number' => $this->generateLicenseNumber()]);
            }

            // Create agent license
            $agent->license()->create([
                'number' => $agent->license_number,
                'type' => $data['license_type'] ?? 'real_estate',
                'issued_at' => now(),
                'expires_at' => now()->addYears(2),
                'status' => 'active'
            ]);

            // Create commission structure
            $agent->commissionStructure()->create([
                'base_rate' => $data['commission_rate'] ?? 2.5,
                'sale_rate' => $data['sale_rate'] ?? 0.025,
                'rental_rate' => $data['rental_rate'] ?? 0.10,
                'referral_rate' => $data['referral_rate'] ?? 0.05,
                'bonus_structure' => $data['bonus_structure'] ?? json_encode([
                    'performance_bonus' => 1000,
                    'referral_bonus' => 500,
                    'team_bonus' => 2000,
                    'leadership_bonus' => 3000
                'training_bonus' => 1000
                'retention_bonus' => 500
                'productivity_bonus' => 2000
                'attendance_bonus' => 1000
                'quality_bonus' => 1000
                'innovation_bonus' => 1000
                'customer_service_bonus' => 1000
                'sales_target_bonus' => 1000
                'team_leadership_bonus' => 1000
                'company_growth_bonus' => 1000
                'year_end_bonus' => 1000
                'quarter_end_bonus' => 1000
                'month_end_bonus' => 1000
                'week_end_bonus' => 1000
                'daily_target_bonus' => 1000
            ])
                ])
            ]);

            // Create initial commission record
            $agent->commissions()->create([
                'type' => 'registration',
                'amount' => 0,
                'rate' => $agent->commissionStructure->base_rate,
                'agent_id' => $agent->id,
                'created_by' => $user->id
            ]);

            Log::info('Agent registered successfully', [
                'agent_id' => $agent->id,
                'user_id' => $agent->id
            ]);

            DB::commit();

            Log::info('Agent registered', [
                'agent_name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'license_number' => $agent->license_number
            ]);

            return $agent->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to register agent', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Assign lead to agent
     */
    public function assignLead(int $agentId, int $leadId, string $note = null): bool
    {
        DB::beginTransaction();
        try {
            $lead = Lead::findOrFail($leadId);
            $agent = Agent::findOrFail($agentId);

            // Check if lead is already assigned
            if ($lead->assigned_agent_id) !== null) {
                throw new \Exception('Lead is already assigned to another agent');
            }

            // Assign lead to agent
            $lead->update([
                'assigned_agent_id' => $agent->id,
                'assigned_at' => now(),
                'assigned_note' => $note,
                'status' => 'assigned'
            ]);

            // Create assignment record
            $lead->assignments()->create([
                'lead_id' => $leadId,
                'agent_id' => $agentId,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'note' => $note,
                'status' => 'assigned'
            ]);

            // Update lead status
            $lead->update([
                'status' => 'assigned',
                'assigned_at' => now()
            ]);

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'عميل جديد مخصص لك',
                'message' => "تم تخصيص العميل {$lead->title} لك.",
                'type' => 'lead_assigned',
                'data' => [
                    'lead_id' => $leadId,
                    'lead_title' => $lead->title,
                    'client_name' => $lead->client->name,
                    'client_phone' => $lead->client->phone,
                    'client_email' => $lead->client->email,
                    'assigned_by' => auth()->name,
                    'assigned_at' => now()
                ]
            ]);

            // Create notification for client if exists
            if ($lead->client) {
                $lead->client->notifications()->create([
                    'title' => 'تم تخصيص عميلك',
                    'message' => "تم تخصيص عميلك {$lead->title} للوكيل {$agent->name}.",
                    'type' => 'lead_assigned_to_client',
                    'data' => [
                        'agent_id' => $agent->id,
                        'agent_name' => $agent->name,
                        'lead_id' => $leadId,
                        'lead_title' => $lead->title,
                        'client_name' => $lead->client->name,
                        'assigned_to' => auth()->name
                    ]
                ]);
            }

            Log::info('Lead assigned to agent', [
                'agent_id' => $agent->id,
                'lead_id' => $leadId,
                'assigned_by_id' => auth()->id()
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign lead to agent', [
                'agent_id' => $agentId,
                'lead_id' => $leadId,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Convert lead to client
     */
    public function convertLeadToClient(int $agentId, int $leadId, float $salePrice, array $saleData = []): bool
    {
        DB::beginTransaction();
        try {
            $lead = Lead::findOrFail($leadId);
            $agent = Agent::findOrFail($agentId);

            // Check if lead is already converted
            if ($lead->status === 'converted') {
                throw new \Exception('Lead is already converted');
            }

            // Create client if not exists
            $client = $this->createClientFromLead($lead, $salePrice, $saleData);

            // Create sale record
            $sale = $agent->sales()->create([
                'agent_id' => $agentId,
                'client_id' => $client->id,
                'property_id' => $lead->property_id,
                'sale_price' => $salePrice,
                'commission_rate' => $agent->commissionStructure->sale_rate,
                'commission_amount' => $salePrice * ($agent->commissionStructure->sale_rate / 100),
                'agent_id' => $agent->id,
                'created_by' => auth()->id(),
                'created_at' => now()
            ]);

            // Update lead status
            $lead->update([
                'status' => 'converted',
                'converted_at' => now(),
                'converted_by' => $agent->id,
                'client_id' => $client->id,
                'sale_id' => $sale->id,
                'sale_price' => $salePrice,
                'commission_amount' => $salePrice * ($agent->commissionStructure->sale_rate / 100)
            ]);

            // Create commission record
            $agent->commissions()->create([
                'type' => 'sale',
                'amount' => $salePrice * ($agent->commissionStructure->sale_rate / 100),
                'rate' => $agent->commissionStructure->sale_rate,
                'agent_id' => $agent->id,
                'sale_id' => $sale->id,
                'client_id' => $client->id,
                'created_by' => auth()->id(),
                'created_at' => now()
            ]);

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'بيع ناجح',
                'message' => "تهانينا! تم بيع العقار {$lead->property->title} للعميل {$client->name} بقيمة {$salePrice}",
                'type' => 'lead_converted_to_client',
                'data' => [
                    'sale_id' => $sale->id,
                    'property_title' => $lead->property->title,
                    'client_name' => $client->name,
                    'sale_price' => $salePrice,
                    'commission_amount' => $salePrice * ($agent->commissionStructure->sale_rate / 100),
                    'client_id' => $client->id,
                    'sale_id' => $sale_id,
                    'commission_id' => $commission->id,
                    'earned_by' => $agent->id
                ]
                ]
            ]);

            // Create notification for client
            $client->notifications()->create([
                'title' => 'تهانينا! تم شراء العقار',
                'message' => "تهانينا! تم شراء العقار {$lead->property->title} من قبل {$agent->name}.",
                'type' => 'property_purchased',
                'data' => [
                    'property_id' => $lead->property_id,
                    'property_title' => $lead->property->title',
                    'agent_id' => $agent->id,
                    'sale_id' => $sale->id,
                    'sale_price' => $salePrice,
                    'purchase_price' => $salePrice,
                    'agent_id' => $agent->id,
                    'earned_by' => $agent->id
                ]
                ]
            ]);

            Log::info('Lead converted to client', [
                'agent_id' => $agent->id,
                'lead_id' => $leadId,
                'client_id' => $client->id,
                'sale_id' => $sale->id,
                'property_id' => $lead->property_id,
                'agent_id' => $agent->id,
                'earned_by' => $agent->id
                ]);
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to convert lead to client', [
                'agent_id' => $agentId,
                'lead_id' => $leadId,
                'sale_price' => $salePrice,
                'error' => $e->getMessage(),
                'data' => $saleData
            ]);
            
            throw $e;
        }
    }

    /**
     * Schedule appointment with client
     */
    public function scheduleAppointment(int $agentId, int $leadId, string $dateTime, string $location, string $note = null): bool
    {
        DB::beginTransaction();
        try {
            $lead = Lead::findOrFail($leadId);
            $client = $lead->client;

            // Create appointment record
            $appointment = $agent->appointments()->create([
                'agent_id' => $agentId,
                'client_id' => $client->id,
                'property_id' => $lead->property_id,
                'start_time' => $dateTime,
                'end_time' => null,
                'location' => $location,
                'status' => 'scheduled',
                'note' => $note,
                'created_at' => now(),
                'created_by' => $agent->id
            ]);

            // Update lead status
            $lead->update([
                'status' => 'appointment_scheduled',
                'appointment_scheduled_at' => now()
            ]);

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'موعد مجدول مع العميل',
                'message' => "تم تحديد موعد مع العميل {$client->name} في {$dateTime} في {$location}",
                'type' => 'appointment_scheduled',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'client_id' => $client->id,
                    'property_id' => $lead->property_id,
                    'start_time' => $dateTime,
                    'location' => $location,
                    'scheduled_by' => $agent->name,
                    'appointment_time' => $dateTime
                ]
                ]
            ]);

            // Create notification for client
            $client->notifications()->create([
                'title' => 'موعد مجدول جديد',
                'message' => "تم تحديد موعد مع العميل {$client->name} في {$dateTime} في {$location}",
                'type' => 'appointment_scheduled',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'client_id' => $client->id,
                    'property_id' => $lead->property_id,
                    'start_time' => $dateTime,
                    'location' => $location,
                    'scheduled_by' => $agent->name,
                    'appointment_time' => $dateTime
                ]
                ]
            ]);

            Log::info('Appointment scheduled with client', [
                'agent_id' => $agentId,
                'lead_id' => $leadId,
                'client_id' => $client->id,
                'property_id' => $lead->property_id,
                'appointment_id' => $appointment_id,
                'start_time' => $dateTime,
                'location' => $location,
                'scheduled_by' => $agent->name
                ]
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to schedule appointment', [
                'agent_id' => $agentId,
                'lead_id' => $leadId,
                'client_id' => $client->id,
                'property_id' => $lead->property_id,
                'appointment_time' => $dateTime,
                'location' => $location,
                'error' => $e->getMessage(),
                'data' => [
                    'dateTime' => $dateTime,
                    'location' => $location
                ]
            ]);
            
            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Appointment scheduling job failed', [
                'agent_id' => $this->agentId,
                'lead_id' => $leadId,
                'client_id' => $client->id,
                'property_id' => $lead->property_id,
                'appointment_time' => $dateTime,
                'location' => $location,
                'error' => $exception->getMessage()
            ]);
        }
    }
    }

    /**
     * Create user account for agent
     */
    private function createUserAccount(array $data): User
    {
        return User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'agent',
            'email_verified_at' => now(),
            'status' => 'active'
            'created_at' => now()
        ]);
    }

    /**
     * Create client from lead data
     */
    private function createClientFromLead(Lead $lead, float $salePrice, array $saleData): Client
    {
        return Client::create([
            'name' => $saleData['client_name'] ?? 'Unknown Client',
            'email' => $saleData['client_email'] ?? null,
            'phone' => $saleData['client_phone'] ?? null,
            'address' => $saleData['client_address'] ?? null,
            'type' => 'buyer',
            'source' => 'lead->source->name ?? 'direct',
            'created_at' => now(),
            'created_by' => $lead->assigned_agent_id
            ]);
    }

    /**
     * Generate license number
     */
    private function generateLicenseNumber(): string
    {
        $lastLicense = Agent::max('license_number') + 1;
        return 'AG' . str_pad($lastLicense, 6, '0, '0);
    }

    /**
     * Calculate commission ranking
     */
    private function calculateCommissionRanking(Agent $agent, float $totalAmount): int
    {
        // This would compare with other agents in the company
        // Placeholder implementation
        
        $companyAgents = $agent->company ? $agent->company->agents()->count() : 0;
        
        if ($companyAgents > 0) {
            $averageAmount = $totalAmount / $companyAgents;
            return $this->getRankingScore($averageAmount);
        }
        
        return $this->getRankingScore($totalAmount);
    }

    /**
     * Get ranking score based on amount
     */
    private function getRankingScore(float $amount): int
    {
        if ($amount >= 10000) {
            return 5; // Top performer
        } elseif ($amount >= 5000) {
            return 4; // Excellent performer
        } elseif ($amount >= 2500) {
            return 3; // Very good performer
        } elseif ($amount >= 1000) {
            return 2; // Good performer
        } elseif ($amount >= 500) {
            return 1; // Average performer
        } else {
            return 0; // Needs improvement
        }
    }
    }
}
