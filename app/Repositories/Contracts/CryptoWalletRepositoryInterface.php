<?php

namespace App\Repositories\Contracts;

interface CryptoWalletRepositoryInterface extends BaseRepositoryInterface
{
    public function getByAddress(string $address);
    public function getUserWallet(int $userId);
    public function getWallets(array $filters = [], int $perPage = 20);
    public function getWallet(int $id = null, string $address = null);
    public function getWalletTypes($startDate);
    public function getNetworks($startDate);
    public function getCurrencies($startDate);
    public function getTopWallets($startDate);
    public function countActive();
    public function sumBalance();
    public function countNewWallets($startDate);
    public function averageBalance();
}
