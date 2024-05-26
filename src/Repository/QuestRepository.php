<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Models\IQuest;
use App\Models\Quest;



class QuestRepository extends Repository implements IQuestRepository
{
  private function constructQuestModel(array $data): IQuest
  {
    return new Quest(
      $data['quest_id'],
      $data['title'],
      $data['description'],
      $data['worth_knowledge'],
      $data['blockchain'],
      $data['required_minutes'],
      $data['expiry_date'],
      $data['participants_count'],
      $data['participants_limit'],
      $data['pool_amount'],
      $data['token'],
      $data['creator_id'],
      $data['approved'],
      $data['picture_url'],
      $data['max_points'],
      $data['payout_date'],
      $data['creator_name']
    );
  }

  private function getTokenId(string $tokenName): int
  {
    $token_id = $this->db->connect()->query("SELECT token_id FROM tokens WHERE name = '" . $tokenName . "'")->fetchColumn();

    if ($token_id === false) {
      $this->db->connect()->query("INSERT INTO tokens (name) VALUES ('" . $tokenName . "')");
      $token_id = $this->db->connect()->lastInsertId();
    }

    return $token_id;
  }

  private function getBlockchainId(string $blockchainName): int
  {
    $blockchain_id = $this->db->connect()->query("SELECT blockchain_id FROM blockchains WHERE name = '" . $blockchainName . "'")->fetchColumn();

    if ($blockchain_id === false) {
      $this->db->connect()->query("INSERT INTO blockchains (name) VALUES ('" . $blockchainName . "')");
      $blockchain_id = $this->db->connect()->lastInsertId();
    }

    return $blockchain_id;
  }

  private function getPictureId(string $pictureUrl): int
  {
    $picture_id = $this->db->connect()->query("SELECT picture_id FROM pictures WHERE picture_url = '" . $pictureUrl . "'")->fetchColumn();

    if ($picture_id === false) {
      $this->db->connect()->query("INSERT INTO pictures (picture_url) VALUES ('" . $pictureUrl . "')");
      $picture_id = $this->db->connect()->lastInsertId();
    }

    return $picture_id;
  }

  public function updateQuest(IQuest $quest)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $token_id = $this->getTokenId($quest->getToken());
      $blockchain_id = $this->getBlockchainId($quest->getBlockchain());
      $picture_id = $this->getPictureId($quest->getPictureUrl());


      $sql = "UPDATE quests SET
              title = :title, 
              description = :description, 
              worth_knowledge = :worth_knowledge, 
              blockchain_id = :blockchain_id, 
              required_minutes = :required_minutes, 
              expiry_date = :expiry_date, 
              participants_count = :participants_count, 
              participants_limit = :participants_limit, 
              pool_amount = :pool_amount, 
              token_id = :token_id, 
              creator_id = :creator_id, 
              approved = :approved, 
              picture_id = :picture_id, 
              max_points = :max_points, 
              payout_date = :payout_date 
              WHERE quest_id = :quest_id";

      $stmt = $this->db->connect()->prepare($sql);

      $stmt->execute([
        ':title' => $quest->getTitle(),
        ':description' => $quest->getDescription(),
        ':worth_knowledge' => $quest->getWorthKnowledge(),
        ':blockchain_id' => $blockchain_id,
        ':required_minutes' => $quest->getRequiredMinutes(),
        ':expiry_date' => $quest->getExpiryDateString(),
        ':participants_count' => $quest->getParticipantsCount(),
        ':participants_limit' => $quest->getParticipantsLimit(),
        ':pool_amount' => $quest->getPoolAmount(),
        ':token_id' => $token_id,
        ':creator_id' => $quest->getCreatorId(),
        ':approved' => $quest->getIsApproved(),
        ':picture_id' => $picture_id,
        ':max_points' => $quest->getMaxPoints(),
        ':payout_date' => $quest->getPayoutDate(),
        ':quest_id' => $quest->getQuestID(),
      ]);

      $pdo->commit();
      return $pdo->lastInsertId();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveQuest(IQuest $quest): int
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $token_id = $this->getTokenId($quest->getToken());
      $blockchain_id = $this->getBlockchainId($quest->getBlockchain());
      $picture_id = $this->getPictureId($quest->getPictureUrl());

      $sql = "INSERT INTO quests (title, description, worth_knowledge, 
              blockchain_id, required_minutes, expiry_date, participants_count, 
              participants_limit, pool_amount, token_id, creator_id, approved, picture_id, max_points, payout_date) 
              VALUES (:title, :description, :worth_knowledge, :blockchain_id, 
              :required_minutes, :expiry_date, :participants_count, :participants_limit, :pool_amount, :token_id, :creator_id, :approved, :picture_id, :max_points, :payout_date)";

      $stmt = $this->db->connect()->prepare($sql);

      $stmt->execute([
        ':title' => $quest->getTitle(),
        ':description' => $quest->getDescription(),
        ':worth_knowledge' => $quest->getWorthKnowledge(),
        ':blockchain_id' => $blockchain_id,
        ':required_minutes' => $quest->getRequiredMinutes(),
        ':expiry_date' => $quest->getExpiryDateString(),
        ':participants_count' => $quest->getParticipantsCount(),
        ':participants_limit' => $quest->getParticipantsLimit(),
        ':pool_amount' => $quest->getPoolAmount(),
        ':token_id' => $token_id,
        ':creator_id' => $quest->getCreatorId(),
        ':approved' => $quest->getIsApproved(),
        ':picture_id' => $picture_id,
        ':max_points' => $quest->getMaxPoints(),
        ':payout_date' => $quest->getPayoutDate(),
      ]);

      $pdo->commit();
      return $pdo->lastInsertId();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }

  }


  public function approve(int $questId)
  {
    $sql = "UPDATE quests 
            SET approved = :approved 
            WHERE quest_id = :quest_id";

    $stmt = $this->db->connect()->prepare($sql);

    $stmt->execute([
      ':approved' => (int) true,
      ':quest_id' => $questId,
    ]);
  }

  public function getQuestById($questId): ?IQuest
  {
    $sql = $this->getQuestQuery('WHERE quest_id = :quest_id');
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':quest_id' => $questId]);
    $questFetched = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($questFetched === false) {
      return null;
    }

    return $this->constructQuestModel($questFetched);
  }

  private function getQuestQuery(string $whereClause = ''): string
  {

    $q = "SELECT quest_id, creator_id, p.picture_url as picture_url, u.username as creator_name,
            b.name as blockchain, t.name as token, title, description, worth_knowledge, expiry_date, participants_count, participants_limit, pool_amount, max_points, required_minutes, approved, payout_date FROM quests q 
            INNER JOIN blockchains b ON b.blockchain_id = q.blockchain_id 
            INNER JOIN users t ON t.user_id = q.creator_id
            INNER JOIN tokens t ON t.token_id = q.token_id 
            INNER JOIN pictures p on p.picture_id = q.picture_id ";

    $q .= $whereClause;

    return $q;
  }

  public function getQuests(): array
  {
    $quests = [];
    $sql = $this->getQuestQuery();
    $stmt = $this->db->connect()->query($sql);
    $fetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->constructQuestModel($fetched_quest);
    }

    return $quests;
  }

  public function getCreatorQuests(int $creator): array
  {
    $quests = [];
    $sql = $this->getQuestQuery('WHERE creator_id = :creator');
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':creator' => $creator]);
    $fetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->constructQuestModel($fetched_quest);
    }

    return $quests;
  }

  public function getApprovedQuests(): array
  {
    return $this->getQuestByApprovedFlag(true);
  }

  public function getQuestToApprove(): array
  {
    return $this->getQuestByApprovedFlag(false);
  }

  private function getQuestByApprovedFlag(bool $isApproved): array
  {
    $quests = [];
    $sql = $this->getQuestQuery('WHERE approved = :approved');
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':approved' => $isApproved]);
    $fetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->constructQuestModel($fetched_quest);
    }

    return $quests;
  }

  public function getAllQuestIds(): array
  {
    $questIds = [];
    $stmt = $this->db->connect()->prepare('SELECT quest_id FROM quests');
    $stmt->execute();
    $fetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $questIds[] = $fetched_quest['quest_id'];
    }

    return $questIds;
  }
}