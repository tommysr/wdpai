<?php

namespace App\Repository;

use App\Models\IWallet;

interface IWalletRepository
{
  public function getBlockchainWallets(int $userId, string $blockchain): array;
  public function addWallet(IWallet $wallet): int;
}