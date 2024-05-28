<?php

namespace App\Services\Wallets;

use App\Models\IWallet;
use App\Repository\IWalletRepository;
use App\Repository\WalletRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Wallets\IWalletService;

class WalletService implements IWalletService
{
  private IWalletRepository $walletRepository;

  public function __construct(IWalletRepository $walletRepository = null)
  {
    $this->walletRepository = $walletRepository ?: new WalletRepository();
  }

  public function getBlockchainWallets(IIdentity $identity, string $blockchain): array
  {
    $userId = $identity->getId();
    return $this->walletRepository->getBlockchainWallets($userId, $blockchain);
  }

  public function createWallet(IWallet $wallet): int
  {
    return $this->walletRepository->addWallet($wallet);
  }
}