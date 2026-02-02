<?php

namespace Database\Seeders;

use App\Models\Dao;
use App\Models\DaoMember;
use App\Models\DaoProposal;
use Illuminate\Database\Seeder;

class DaoSeeder extends Seeder
{
    public function run(): void
    {
        $daos = [
            [
                'name' => 'DeFi Governance DAO',
                'description' => 'A decentralized autonomous organization for governing DeFi protocols and making collective decisions about the platform\'s future.',
                'purpose' => 'Protocol Governance',
                'token_symbol' => 'DEFI',
                'total_supply' => 1000000.00,
                'voting_power' => 1000000.00,
                'quorum' => 51.00,
                'voting_period' => 7,
                'contract_address' => '0x1234567890abcdef1234567890abcdef12345678',
                'status' => 'active',
                'creator_id' => 1
            ],
            [
                'name' => 'Treasury Management DAO',
                'description' => 'Manages the treasury funds and decides on investment strategies for the ecosystem growth.',
                'purpose' => 'Treasury Management',
                'token_symbol' => 'TREAS',
                'total_supply' => 500000.00,
                'voting_power' => 500000.00,
                'quorum' => 60.00,
                'voting_period' => 14,
                'contract_address' => '0xabcdef1234567890abcdef1234567890abcdef12',
                'status' => 'active',
                'creator_id' => 2
            ],
            [
                'name' => 'Community Grant DAO',
                'description' => 'Distributes grants to community projects and developers building on the platform.',
                'purpose' => 'Grant Distribution',
                'token_symbol' => 'GRANT',
                'total_supply' => 250000.00,
                'voting_power' => 250000.00,
                'quorum' => 40.00,
                'voting_period' => 10,
                'contract_address' => null,
                'status' => 'proposed',
                'creator_id' => 1
            ]
        ];

        foreach ($daos as $daoData) {
            $dao = Dao::create($daoData);
            
            // Add members
            for ($i = 1; $i <= 2; $i++) {
                DaoMember::create([
                    'dao_id' => $dao->id,
                    'user_id' => $i,
                    'voting_power' => $dao->total_supply / 2,
                    'tokens_held' => $dao->total_supply / 2,
                    'role' => $i === 1 ? 'admin' : 'member',
                    'joined_at' => now()
                ]);
            }
            
            // Add sample proposals
            if ($dao->status === 'active') {
                DaoProposal::create([
                    'dao_id' => $dao->id,
                    'proposer_id' => 1,
                    'title' => 'Increase Treasury Allocation for Marketing',
                    'description' => 'Proposal to allocate 50,000 tokens for marketing campaigns to increase platform adoption.',
                    'type' => 'funding',
                    'amount_requested' => 50000.00,
                    'recipient_address' => '0x9876543210fedcba9876543210fedcba98765432',
                    'status' => 'active',
                    'voting_starts_at' => now(),
                    'voting_ends_at' => now()->addDays($dao->voting_period),
                    'votes_for' => 1,
                    'votes_against' => 0,
                    'votes_abstain' => 1
                ]);
                
                DaoProposal::create([
                    'dao_id' => $dao->id,
                    'proposer_id' => 2,
                    'title' => 'Modify Voting Period from 7 to 14 days',
                    'description' => 'Extend the voting period to allow more community members to participate in decisions.',
                    'type' => 'parameter_change',
                    'status' => 'passed',
                    'voting_starts_at' => now()->subDays(10),
                    'voting_ends_at' => now()->subDays(3),
                    'votes_for' => 2,
                    'votes_against' => 0,
                    'votes_abstain' => 0,
                    'execution_result' => 'Successfully implemented voting period extension.'
                ]);
            }
        }
    }
}
