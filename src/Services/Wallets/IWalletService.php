<?php

namespace App\Services\Wallets;

use App\Models\IWallet;
use App\Services\Authenticate\IIdentity;

interface IWalletService
{
  // public function getWallets(): array;
  public function getBlockchainWallets(IIdentity $identity, string $blockchain): array;
  // public function getWallet(int $walletId): ?IWallet;
  public function createWallet(IIdentity $identity, string $blockchain, string $address): int;
}