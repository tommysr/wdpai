<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/QuestStatistics.php';

class QuestStatisticsRepository extends Repository
{
  public function getQuestStatistic($userId, $questId): ?QuestStatistics
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

    return new QuestStatistics($statistics['completiondate'], $statistics['score'], $statistics['userid'], $statistics['questid']);
  }
}