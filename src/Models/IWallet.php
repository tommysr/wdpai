<?php

namespace App\Models;

interface IWallet
{
  public function getWalletId(): int;
  public function getUserId(): int;
  public function getBlockchain(): string;
  public function getWalletAddress(): string;
  public function getCreatedAt(): string;
  public function getUpdatedAt(): string;
}