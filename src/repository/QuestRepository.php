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

  public function updateQuest(Quest $quest)
  {
    $sql = "UPDATE quests SET
              title = :title, 
              description = :description, 
              worthknowledge = :worthknowledge, 
              requiredwallet = :requiredwallet, 
              expirydate = :expirydate, 
              participantscount = :participantscount, 
              participantlimit = :participantlimit, 
              poolamount = :poolamount, 
              token = :token, 
              points = :points, 
              timerequired_minutes = :timerequired_minutes,
              creator = :creator, 
              approved = :approved 
              WHERE questid = :questid";

    $stmt = $this->db->connect()->prepare($sql);

    $stmt->execute([
      ':questid' => $quest->getQuestID(),
      ':title' => $quest->getTitle(),
      ':description' => $quest->getDescription(),
      ':worthknowledge' => $quest->getWorthKnowledge(),
      ':requiredwallet' => $quest->getRequiredWallet(),
      ':expirydate' => $quest->getExpiryDateString(),
      ':participantscount' => $quest->getParticipantsCount(),
      ':participantlimit' => $quest->getParticipantLimit(),
      ':poolamount' => $quest->getPoolAmount(),
      ':token' => $quest->getToken(),
      ':points' => $quest->getPoints(),
      ':timerequired_minutes' => $quest->getTimeRequiredMinutes(),
      ':creator' => $quest->getCreatorId(),
      ':approved' => (int) $quest->isApproved(),
    ]);

  }


  public function saveQuest(Quest $quest): int
  {
    $sql = "INSERT INTO quests (title, description, worthknowledge, requiredwallet, expirydate, participantscount, participantlimit, poolamount, token, points, timerequired_minutes, creator, approved)
    VALUES (:title, :description, :worthknowledge, :requiredwallet, :expirydate, :participantscount, :participantlimit, :poolamount, :token, :points, :timerequired_minutes, :creator, :approved)";

    $stmt = $this->db->connect()->prepare($sql);

    $stmt->execute([
      ':title' => $quest->getTitle(),
      ':description' => $quest->getDescription(),
      ':worthknowledge' => $quest->getWorthKnowledge(),
      ':requiredwallet' => $quest->getRequiredWallet(),
      ':expirydate' => $quest->getExpiryDateString(),
      ':participantscount' => $quest->getParticipantsCount(),
      ':participantlimit' => $quest->getParticipantLimit(),
      ':poolamount' => $quest->getPoolAmount(),
      ':token' => $quest->getToken(),
      ':points' => $quest->getPoints(),
      ':timerequired_minutes' => $quest->getTimeRequiredMinutes(),
      ':creator' => $quest->getCreatorId(),
      ':approved' => $quest->isApproved(),
    ]);


    return $this->db->connect()->lastInsertId();
  }


  public function approve(int $questId)
  {
    $sql = "UPDATE quests SET
    approved = :approved 
    WHERE questid = :questid";

    $stmt = $this->db->connect()->prepare($sql);

    $stmt->execute([
      ':approved' => (int) true,
      ':questid' => $questId,
    ]);
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

  public function getCreatorQuests(int $creator): array
  {
    $quests = [];
    $stmt = $this->db->connect()->prepare('SELECT * FROM quests WHERE creator = :creator');
    $stmt->execute([':creator' => $creator]);
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $stmt = $this->db->connect()->prepare('SELECT * FROM quests WHERE approved = :approved');
    $stmt->execute(['approved' => $isApproved]);
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched as $fetched_quest) {
      $quests[] = $this->constructQuestModel($fetched_quest);
    }

    return $quests;
  }
}