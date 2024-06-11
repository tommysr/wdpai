<?php

namespace App\Services\Wallets;

use App\Models\IWallet;
use App\Models\Wallet;
use App\Repository\IWalletRepository;
use App\Repository\WalletRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Wallets\IWalletService;

class WalletService implements IWalletService
{
  private IWalletRepository $walletRepository;

  public function __construct(IWalletRepository $walletRepository)
  {
    $this->walletRepository = $walletRepository;
  }

  public function getBlockchainWallets(IIdentity $identity, string $blockchain): array
  {
    $userId = $identity->getId();
    return $this->walletRepository->getBlockchainWallets($userId, $blockchain);
  }

  public function createWallet(IIdentity $identity, string $blockchain, string $address): int
  {
    $userId = $identity->getId();
    $wallet = new Wallet(0, $userId, $blockchain, $address, date('Y-m-d'), date('Y-m-d'));
    return $this->walletRepository->addWallet($wallet);
  }
}