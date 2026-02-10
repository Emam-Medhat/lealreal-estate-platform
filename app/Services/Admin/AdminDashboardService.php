<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Contracts\InvestorRepositoryInterface;
use Carbon\Carbon;

class AdminDashboardService
{
    protected $userRepository;
    protected $propertyRepository;
    protected $companyRepository;
    protected $investorRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PropertyRepositoryInterface $propertyRepository,
        CompanyRepositoryInterface $companyRepository,
        InvestorRepositoryInterface $investorRepository
    ) {
        $this->userRepository = $userRepository;
        $this->propertyRepository = $propertyRepository;
        $this->companyRepository = $companyRepository;
        $this->investorRepository = $investorRepository;
    }

    public function getDashboardStats(): array
    {
        $today = Carbon::today()->toDateString();

        return [
            'site' => [
                'total_users' => $this->userRepository->count(),
                'new_users_today' => $this->userRepository->countByDate($today),
                'total_properties' => $this->propertyRepository->count(),
                'new_properties_today' => $this->propertyRepository->countByDate($today),
                'total_agents' => $this->userRepository->countByType('agent'),
                'total_companies' => $this->companyRepository->count(),
                'new_companies_today' => $this->companyRepository->countByDate($today),
                'active_properties' => $this->propertyRepository->countByStatus('active'),
                'sold_properties' => $this->propertyRepository->countByStatus('sold'),
                'total_investors' => $this->investorRepository->count(),
                'new_investors_today' => $this->investorRepository->countByDate($today),
                'total_revenue' => 0,
                'revenue_today' => 0,
            ],
            'recent_users' => $this->userRepository->getRecent(5),
            'recent_properties' => $this->propertyRepository->getLatest(5),
            'recent_activity' => [
                ['icon' => 'users', 'message' => 'System initialized', 'time' => now()->diffForHumans()],
            ],
        ];
    }
}
