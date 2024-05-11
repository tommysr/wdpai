<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/QuestStatistics.php';

class QuestStatisticsRepository extends Repository
{
  public function getQuestIdToFinish(int $userId): ?int {
    $sql = "SELECT questid FROM QuestStatistics WHERE userid = :user_id AND state = 'STATE_IN_PROGRESS'";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    $questId = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $len = sizeof($questId);
    if ($len > 1) {
      throw new Exception("User has more than one quest in progress");
    }
    
    return sizeof($questId) > 0 ? $questId[0]['questid'] : null;
  }

  public function getQuestStatistic(int $userId, int $questId): ?QuestStatistics
  {
    $sql = "SELECT *
              FROM QuestStatistics qs
              INNER JOIN Users u ON qs.UserID = u.UserID
              WHERE u.UserID = :userId
              AND qs.QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['userId' => $userId, 'questId' => $questId]);
    $statistics = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$statistics) {
      return null;
    }

    return new QuestStatistics($statistics['completiondate'], $statistics['score'], $statistics['userid'], $statistics['questid'], $statistics['walletid'], $statistics['last_question_index'], $statistics['state']);
  }

  public function addParticipation(int $userId, int $questId, int $walletId): void
  {
    $sql = "INSERT INTO QuestStatistics (userid, questid, walletid, score)
              VALUES (:userId, :questId, :walletId, 0)";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':userId' => $userId, ':questId' => $questId, ':walletId' => $walletId]);
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