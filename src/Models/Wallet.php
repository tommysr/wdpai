<?php

namespace App\Models;
use App\Models\IWallet;

class Wallet implements IWallet
{
  private int $walletId;
  private int $userId;
  private string $blockchain;
  private string $walletAddress;
  private string $createdAt;
  private string $updatedAt;

  public function __construct(int $walletId, int $userId, string $blockchain, string $walletAddress, string $createdAt, string $updatedAt)
  {
    $this->walletId = $walletId;
    $this->userId = $userId;
    $this->blockchain = $blockchain;
    $this->walletAddress = $walletAddress;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public function getWalletId(): int
  {
    return $this->walletId;
  }

  public function getUserId(): int
  {
    return $this->userId;
  }

  public function getBlockchain(): string
  {
    return $this->blockchain;
  }

  public function getWalletAddress(): string
  {
    return $this->walletAddress;
  }

  public function getCreatedAt(): string
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): string
  {
    return $this->updatedAt;
  }
}