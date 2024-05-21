<?php

namespace App\Repository;

use App\Repository\IQuestStatisticsRepository;
use App\Repository\Repository;
use App\Models\IQuestStatistics;
use App\Models\QuestStatistics;


class QuestStatisticsRepository extends Repository implements IQuestStatisticsRepository
{
  public function getQuestIdToFinish(int $userId): ?int
  {
    $sql = "SELECT questid FROM QuestStatistics WHERE userid = :user_id AND state = 'STATE_IN_PROGRESS'";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    $questId = $stmt->fetchAll(\PDO::FETCH_ASSOC);


    $len = sizeof($questId);
    if ($len > 1) {
      throw new \Exception("User has more than one quest in progress");
    }

    return sizeof($questId) > 0 ? $questId[0]['questid'] : null;
  }

  public function getQuestStatistics(int $userId, int $questId): ?IQuestStatistics
  {
    $sql = "SELECT *
              FROM QuestStatistics qs
              INNER JOIN Users u ON qs.UserID = u.UserID
              WHERE u.UserID = :userId
              AND qs.QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['userId' => $userId, 'questId' => $questId]);
    $statistics = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$statistics) {
      return null;
    }

    return new QuestStatistics($statistics['completiondate'], $statistics['score'], $statistics['userid'], $statistics['questid'], $statistics['walletid'], $statistics['last_question_index'], $statistics['state']);
  }

  // public function addParticipation(int $userId, int $questId, int $walletId): void
  // {
  //   $sql = "INSERT INTO QuestStatistics (userid, questid, walletid, score)
  //             VALUES (:userId, :questId, :walletId, 0)";

  //   $stmt = $this->db->connect()->prepare($sql);
  //   $stmt->execute([':userId' => $userId, ':questId' => $questId, ':walletId' => $walletId]);
  // }

  public function saveQuestStatistics(IQuestStatistics $questStatistics): int
  {
    $sql = "INSERT INTO QuestStatistics (userid, questid, walletid, score, completiondate, last_question_index, state)
              VALUES (:userId, :questId, :walletId, :score, :completionDate, :lastQuestionIndex, :state)";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([
      ':userId' => $questStatistics->getUserId(),
      ':questId' => $questStatistics->getQuestId(),
      ':walletId' => $questStatistics->getWalletId(),
      ':score' => $questStatistics->getScore(),
      ':completionDate' => $questStatistics->getCompletionDate(),
      ':lastQuestionIndex' => $questStatistics->getLastQuestionId(),
      ':state' => $questStatistics->getState()
    ]);

    return (int) $this->db->connect()->lastInsertId();
  }

  public function updateQuestStatistics(IQuestStatistics $questStatistics)
  {
    $sql = "UPDATE QuestStatistics
              SET score = :score,
                  completiondate = :completionDate,
                  last_question_index = :lastQuestionIndex,
                  state = :state
              WHERE userid = :userId
              AND questid = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([
      ':score' => $questStatistics->getScore(),
      ':completionDate' => $questStatistics->getCompletionDate(),
      ':lastQuestionIndex' => $questStatistics->getLastQuestionId(),
      ':state' => $questStatistics->getState(),
      ':userId' => $questStatistics->getUserId(),
      ':questId' => $questStatistics->getQuestId()
    ]);
  }





  // public function getParticipated(int $userId): array
  // {
  //   $result = [];
  //   $sql = "SELECT *
  //             FROM QuestStatistics qs
  //             INNER JOIN Users u ON qs.UserID = u.UserID
  //             WHERE u.UserID = :userId";

  //   $stmt = $this->db->connect()->prepare($sql);
  //   $stmt->execute(['userId' => $userId]);
  //   $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //   foreach ($statistics as $statistic) {
  //     $result[] = new QuestStatistics($statistic['completiondate'], $statistic['score'], $statistic['userid'], $statistic['questid']);
  //   }

  //   return $result;
  // }
}