<?php

namespace App\Services\Wallets;

use App\Models\IWallet;
use App\Services\Authenticate\IIdentity;

interface IWalletService
{
  public function getBlockchainWallets(IIdentity $identity, string $blockchain): array;
  public function createWallet(IIdentity $identity, string $blockchain, string $address): int;
}