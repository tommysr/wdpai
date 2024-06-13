<?php

use PHPUnit\Framework\TestCase;
use App\Services\Wallets\WalletService;
use App\Repository\IWalletRepository;
use App\Services\Authenticate\IIdentity;

class WalletServiceTest extends TestCase
{
  private $walletRepository;
  private $identity;
  private $walletService;

  protected function setUp(): void
  {
    $this->walletRepository = $this->createMock(IWalletRepository::class);
    $this->identity = $this->createMock(IIdentity::class);
    $this->walletService = new WalletService($this->walletRepository);
  }

  public function testGetBlockchainWallets()
  {
    $userId = 1;
    $blockchain = 'blockchain1';
    $wallets = ['wallet1', 'wallet2'];

    $this->identity->method('getId')->willReturn($userId);
    $this->walletRepository->method('getBlockchainWallets')->willReturn($wallets);

    $result = $this->walletService->getBlockchainWallets($this->identity, $blockchain);

    $this->assertEquals($wallets, $result);
  }

  public function testCreateWallet()
  {
    $userId = 1;
    $blockchain = 'blockchain1';
    $address = 'address1';
    $walletId = 1;

    $this->identity->method('getId')->willReturn($userId);
    $this->walletRepository->method('addWallet')->willReturn($walletId);

    $result = $this->walletService->createWallet($this->identity, $blockchain, $address);

    $this->assertEquals($walletId, $result);
  }
}