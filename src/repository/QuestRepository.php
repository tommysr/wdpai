<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Quest.php';

class QuestRepository extends Repository
{
  public function getQuests(): array
  {
    $quests = [];
    $stmt = $this->db->connect()->prepare('
      SELECT * FROM quests;
    ');
    $stmt->execute();
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = new Quest(
        $fetched_quest['questid'], 
        $fetched_quest['title'], 
        $fetched_quest['description'], 
        $fetched_quest['worthknowledge'], 
        $fetched_quest['requiredwallet'], 
        $fetched_quest['timerequired'], 
        $fetched_quest['expirydate'], 
        $fetched_quest['participantscount'], 
        $fetched_quest['participantlimit'], 
        $fetched_quest['poolamount']);
    }

    return $quests;
  }
}