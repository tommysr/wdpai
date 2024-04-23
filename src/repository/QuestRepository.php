<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Quest.php';

class QuestRepository extends Repository
{
  private function createModel(array $data): Quest
  {
    return new Quest(
      $data['questid'],
      $data['title'],
      $data['description'],
      $data['worthknowledge'],
      $data['requiredwallet'],
      $data['timerequired'],
      $data['expirydate'],
      $data['participantscount'],
      $data['participantlimit'],
      $data['poolamount']
    );
  }


  public function getQuestById($questId): ?Quest
  {
    $sql = "SELECT *
    FROM Quests q
    WHERE q.QuestID = :questId;";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questId' => $questId]);
    $questFetched = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($questFetched === false) {
      return null;
    }

    return $this->createModel($questFetched);
  }

  public function getQuests(): array
  {
    $quests = [];
    $stmt = $this->db->connect()->prepare('
      SELECT * FROM quests;
    ');
    $stmt->execute();
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->createModel($fetched_quest);
    }

    return $quests;
  }
}