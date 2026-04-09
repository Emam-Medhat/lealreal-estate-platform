<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Repositories\Eloquent\AgentRepository;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Repositories\Eloquent\CompanyRepository;
use App\Repositories\Contracts\AdAnalyticsRepositoryInterface;
use App\Repositories\Eloquent\AdAnalyticsRepository;
use App\Repositories\Contracts\ServiceProviderRepositoryInterface;
use App\Repositories\Eloquent\ServiceProviderRepository;
use App\Repositories\Contracts\InvestorRepositoryInterface;
use App\Repositories\Eloquent\InvestorRepository;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use App\Repositories\Eloquent\ActivityLogRepository;
use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;
use App\Repositories\Eloquent\DocumentTemplateRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            DocumentTemplateRepositoryInterface::class,
            DocumentTemplateRepository::class
        );

        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\LeadRepositoryInterface::class,
            \App\Repositories\Eloquent\LeadRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PropertyRepositoryInterface::class,
            \App\Repositories\Eloquent\PropertyRepository::class
        );

        $this->app->bind(
            AgentRepositoryInterface::class,
            AgentRepository::class
        );

        $this->app->bind(
            CompanyRepositoryInterface::class,
            CompanyRepository::class
        );
        
        $this->app->bind(
            InvestorRepositoryInterface::class,
            InvestorRepository::class
        );

        $this->app->bind(
            AdAnalyticsRepositoryInterface::class,
            AdAnalyticsRepository::class
        );

        $this->app->bind(
            ServiceProviderRepositoryInterface::class,
            ServiceProviderRepository::class
        );

        // Check if User Repository files exist before binding to avoid errors if they are placeholders
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AgentLeadRepositoryInterface::class,
            \App\Repositories\Eloquent\AgentLeadRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\InvoiceRepositoryInterface::class,
            \App\Repositories\Eloquent\InvoiceRepository::class
        );

        $this->app->bind(
            \App\Repositories\InvoiceRepositoryInterface::class,
            \App\Repositories\InvoiceRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ChatRoomRepositoryInterface::class,
            \App\Repositories\Eloquent\ChatRoomRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ConversationRepositoryInterface::class,
            \App\Repositories\Eloquent\ConversationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\MessageRepositoryInterface::class,
            \App\Repositories\Eloquent\MessageRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AuctionRepositoryInterface::class,
            \App\Repositories\Eloquent\AuctionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AuctionResultRepositoryInterface::class,
            \App\Repositories\Eloquent\AuctionResultRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\SmartContractRepositoryInterface::class,
            \App\Repositories\Eloquent\SmartContractRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\NftRepositoryInterface::class,
            \App\Repositories\Eloquent\NftRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\BlockchainTransactionRepositoryInterface::class,
            \App\Repositories\Eloquent\BlockchainTransactionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\BlockchainRecordRepositoryInterface::class,
            \App\Repositories\Eloquent\BlockchainRecordRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DaoRepositoryInterface::class,
            \App\Repositories\Eloquent\DaoRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DaoMemberRepositoryInterface::class,
            \App\Repositories\Eloquent\DaoMemberRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DaoProposalRepositoryInterface::class,
            \App\Repositories\Eloquent\DaoProposalRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CryptoWalletRepositoryInterface::class,
            \App\Repositories\Eloquent\CryptoWalletRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CryptoTransactionRepositoryInterface::class,
            \App\Repositories\Eloquent\CryptoTransactionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\BlockchainVerificationRepositoryInterface::class,
            \App\Repositories\Eloquent\BlockchainVerificationRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PaymentRepositoryInterface::class,
            \App\Repositories\Eloquent\PaymentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ClientRepositoryInterface::class,
            \App\Repositories\Eloquent\ClientRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
