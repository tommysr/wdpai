<?php

namespace App\Repository;

use App\Repository\IWalletRepository;
use App\Repository\Repository;
use App\Models\IWallet;
use App\Models\Wallet;

class WalletRepository extends Repository implements IWalletRepository
{

  public function getBlockchainWallets(int $userId, string $blockchain): array
  {
    $sql = "SELECT w.wallet_id, w.user_Id, b.name as blockchain, w.address, w.created_at, w.updated_at
            FROM wallets w
            INNER JOIN blockchains b ON w.blockchain_id = b.blockchain_id
            WHERE w.user_id = :user_id 
            AND b.name = :blockchain";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':blockchain' => $blockchain]);
    $walletsFetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $wallets = [];
    foreach ($walletsFetched as $wallet) {
      $wallets[] = new Wallet($wallet['wallet_id'], $wallet['user_id'], $wallet['blockchain'], $wallet['address'], $wallet['created_at'], $wallet['updated_at']);
    }

    return $wallets;
  }

  public function getWalletAddress(int $walletId): string
  {
    $sql = 'SELECT address from WALLETS where wallet_id = :wallet_id';

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':wallet_id' => $walletId]);

    return (string) $stmt->fetchColumn();
  }

  private function getBlockchainId(\PDO &$pdo, string $blockchain): int
  {
    $sql = "SELECT blockchain_id FROM blockchains WHERE name = :blockchain";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':blockchain' => $blockchain]);

    return (int) $stmt->fetchColumn();
  }

  public function addWallet(IWallet $wallet): int
  {
    $pdo = $this->db->connect();
    $blockchainId = $this->getBlockchainId($pdo, $wallet->getBlockchain());
    $sql = "INSERT INTO wallets (user_id, blockchain_id, address, created_at, updated_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      $wallet->getUserId(),
      $blockchainId,
      $wallet->getWalletAddress(),
      $wallet->getCreatedAt(),
      $wallet->getUpdatedAt()
    ]);

    return $pdo->lastInsertId();
  }
}
