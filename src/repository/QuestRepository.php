<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Quest.php';

class QuestRepository extends Repository
{
  private function constructQuestModel(array $data): Quest
  {
    return new Quest(
      $data['questid'],
      $data['title'],
      $data['description'],
      $data['worthknowledge'],
      $data['requiredwallet'],
      $data['timerequired_minutes'],
      $data['expirydate'],
      $data['participantscount'],
      $data['participantlimit'],
      $data['poolamount'],
      $data['token'],
      $data['points'],
      $data['creator'],
      $data['approved']
    );
  }


  public function getQuestById($questId): ?Quest
  {
    $sql = "SELECT * FROM quests WHERE QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questId' => $questId]);
    $questFetched = $stmt->fetch(PDO::FETCH_ASSOC);


    if ($questFetched === false) {
      return null;
    }

    return $this->constructQuestModel($questFetched);
  }

  public function getQuests(): array
  {
    $quests = [];
    $stmt = $this->db->connect()->prepare('SELECT * FROM quests');
    $stmt->execute();
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->constructQuestModel($fetched_quest);
    }

    return $quests;
  }

  public function getApprovedQuests(): array
  {
    $quests = $this->getQuests();
    $approvedQuests = [];

    foreach ($quests as $quest) {
      if (!$quest->isApproved()) {
        $approvedQuests[] = $quest;
      }
    }

    return $approvedQuests;
  }

}