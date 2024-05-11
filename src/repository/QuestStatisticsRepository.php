<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/QuestStatistics.php';

class QuestStatisticsRepository extends Repository
{
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

    return new QuestStatistics($statistics['completiondate'], $statistics['score'], $statistics['userid'], $statistics['questid'], $statistics['walletid']);
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