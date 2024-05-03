<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Wallet.php';

class WalletRepository extends Repository
{


  public function getBlockchainWallets(int $userId, string $blockchain): array
  {
    $sql = "SELECT *
    FROM UserWallets w
    WHERE w.UserID = :userId 
    AND w.Blockchain = :blockchain;";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['userId' => $userId, 'blockchain' => $blockchain]);
    $walletsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $wallets = [];
    foreach ($walletsFetched as $wallet) {
      $wallets[] = new Wallet($wallet['walletid'], $wallet['userid'], $wallet['blockchain'], $wallet['walletaddress'], $wallet['createdat'], $wallet['updatedat']);
    }

    return $wallets;
  }

  public function addWallet(Wallet $wallet): int
  {
    $sql = 'INSERT INTO UserWallets (UserID, Blockchain, WalletAddress, CreatedAt, UpdatedAt) VALUES (?, ?, ?, ?, ?);';

    $stmt = $this->db->connect()->prepare($sql);

    $stmt->execute([
      $wallet->getUserId(),
      $wallet->getBlockchain(),
      $wallet->getWalletAddress(),
      $wallet->getCreatedAt(),
      $wallet->getUpdatedAt()
    ]);

    return $this->db->connect()->lastInsertId();
  }
}
