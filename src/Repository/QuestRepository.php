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
      $data['avg_rating'],
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

  private function getTokenId(\PDO &$pdo, string $tokenName): int
  {
    $token_id = $pdo->query("SELECT token_id FROM tokens WHERE name = '" . $tokenName . "'")->fetchColumn();

    if (!$token_id) {
      $pdo->query("INSERT INTO tokens (name) VALUES ('" . $tokenName . "')");
      $token_id = $pdo->lastInsertId();
    }

    return $token_id;
  }

  private function getBlockchainId(\PDO &$pdo, string $blockchainName): int
  {
    $blockchain_id = $pdo->query("SELECT blockchain_id FROM blockchains WHERE name = '" . $blockchainName . "'")->fetchColumn();

    if (!$blockchain_id) {
      $pdo->query("INSERT INTO blockchains (name) VALUES ('" . $blockchainName . "')");
      $blockchain_id = $pdo->lastInsertId();
    }

    return $blockchain_id;
  }

  private function getPictureId(\PDO &$pdo, string $pictureUrl): int
  {
    $picture_id = $pdo->query("SELECT picture_id FROM pictures WHERE picture_url = '" . $pictureUrl . "'")->fetchColumn();

    if (!$picture_id) {
      $pdo->query("INSERT INTO pictures (picture_url) VALUES ('" . $pictureUrl . "')");
      $picture_id = $pdo->lastInsertId();
    }

    return $picture_id;
  }

  public function updateQuest(IQuest $quest)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();


    try {
      $token_id = $this->getTokenId($pdo, $quest->getToken());
      $blockchain_id = $this->getBlockchainId($pdo, $quest->getBlockchain());
      $picture_id = $this->getPictureId($pdo, $quest->getPictureUrl());


      $sql = "UPDATE quests SET
              title = :title, 
              description = :description, 
              blockchain_id = :blockchain_id, 
              required_minutes = :required_minutes, 
              expiry_date = :expiry_date, 
              participants_limit = :participants_limit, 
              pool_amount = :pool_amount, 
              token_id = :token_id, 
              approved = :approved, 
              picture_id = :picture_id, 
              payout_date = :payout_date 
              WHERE quest_id = :quest_id";

      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':title' => $quest->getTitle(),
        ':description' => $quest->getDescription(),
        ':blockchain_id' => $blockchain_id,
        ':required_minutes' => $quest->getRequiredMinutes(),
        ':expiry_date' => $quest->getExpiryDateString(),
        ':participants_limit' => $quest->getParticipantsLimit(),
        ':pool_amount' => $quest->getPoolAmount(),
        ':token_id' => $token_id,
        ':approved' => $quest->getIsApproved() ? 1 : 0,
        ':picture_id' => $picture_id,
        ':payout_date' => $quest->getPayoutDate(),
        ':quest_id' => $quest->getQuestID(),
      ]);


      $pdo->commit();

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
      $token_id = $this->getTokenId($pdo, $quest->getToken());
      $blockchain_id = $this->getBlockchainId($pdo, $quest->getBlockchain());
      $picture_id = $this->getPictureId($pdo, $quest->getPictureUrl());

      $sql = "INSERT INTO quests (title, description,
              blockchain_id, required_minutes, expiry_date,
              participants_limit, pool_amount, token_id, creator_id, approved, picture_id, payout_date) 
              VALUES (:title, :description, :blockchain_id, 
              :required_minutes, :expiry_date, :participants_limit, :pool_amount, :token_id, :creator_id, :approved, :picture_id, :payout_date)";

      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':title' => $quest->getTitle(),
        ':description' => $quest->getDescription(),
        ':blockchain_id' => $blockchain_id,
        ':required_minutes' => $quest->getRequiredMinutes(),
        ':expiry_date' => $quest->getExpiryDateString(),
        ':participants_limit' => $quest->getParticipantsLimit(),
        ':pool_amount' => $quest->getPoolAmount(),
        ':token_id' => $token_id,
        ':creator_id' => $quest->getCreatorId(),
        ':approved' => $quest->getIsApproved() ? 1 : 0,
        ':picture_id' => $picture_id,
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

    $q = "SELECT
    Q.QUEST_ID,
    CREATOR_ID,
    P.PICTURE_URL AS PICTURE_URL,
    U.USERNAME AS CREATOR_NAME,
    B.NAME AS BLOCKCHAIN,
    T.NAME AS TOKEN,
    TITLE,
    DESCRIPTION,
    EXPIRY_DATE,
    PARTICIPANTS_LIMIT,
    POOL_AMOUNT,
    REQUIRED_MINUTES,
    APPROVED,
    PAYOUT_DATE,
    COUNT(DISTINCT QP.WALLET_ID) AS PARTICIPANTS_COUNT,
    SUM(QT.POINTS) AS MAX_POINTS,
    AVG(R.RATING) AS AVG_RATING
  FROM
    QUESTS Q
    INNER JOIN BLOCKCHAINS B ON B.BLOCKCHAIN_ID = Q.BLOCKCHAIN_ID
    INNER JOIN USERS U ON U.USER_ID = Q.CREATOR_ID
    INNER JOIN TOKENS T ON T.TOKEN_ID = Q.TOKEN_ID
    LEFT JOIN QUEST_PROGRESS QP ON QP.QUEST_ID = Q.QUEST_ID
    LEFT JOIN QUESTIONS QT ON QT.QUEST_ID = Q.QUEST_ID
    LEFT JOIN RATINGS R ON R.QUEST_ID = Q.QUEST_ID
    INNER JOIN PICTURES P ON P.PICTURE_ID = Q.PICTURE_ID
  GROUP BY
    Q.QUEST_ID,
    Q.CREATOR_ID,
    P.PICTURE_URL,
    U.USERNAME,
    B.NAME,
    T.NAME,
    Q.TITLE,
    Q.DESCRIPTION,
    Q.EXPIRY_DATE,
    Q.PARTICIPANTS_LIMIT,
    Q.POOL_AMOUNT,
    Q.REQUIRED_MINUTES,
    Q.APPROVED,
    Q.PAYOUT_DATE;";

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