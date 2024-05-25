<?php

namespace App\Repository\QuestProgress;

use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\Repository;
use App\Models\Interfaces\IQuestProgress;
use App\Repository\QuestProgress\IQuestProgressRepository;


class QuestProgressRepository extends Repository implements IQuestProgressRepository
{
  public function getInProgress(int $userId): ?QuestProgress
  {
    $stateInProgress = QuestState::InProgress;
    $stateInt = $stateInProgress->getStateId();
    $sql = "SELECT * FROM quest_progress qp 
              INNER JOIN wallets w ON qp.wallet_id = w.wallet_id 
              INNER JOIN users ON w.user_id = u.user_id 
              WHERE state = :state AND u.user_id = :user_id";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':state' => $stateInt, ':user_id' => $userId]);
    $qp = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$qp) {
      return null;
    }

    return new QuestProgress($qp['completion_date'], $qp['score'], $qp['quest_id'], $qp['wallet_id'], $qp['last_question_id'], QuestState::fromId($qp['state']));
  }

  public function getQuestProgress(int $userId, int $questId): ?IQuestProgress
  {
    $sql = "SELECT * FROM quest_progress qp
              INNER JOIN wallets w ON qp.wallet_id = w.wallet_id
              INNER JOIN users u ON w.user_id = u.user_id
              WHERE u.user_id = :user_id AND qp.quest_id = :quest_id";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':quest_id' => $questId]);
    $qp = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$qp) {
      return null;
    }

    return new QuestProgress($qp['completion_date'], $qp['score'], $qp['quest_id'], $qp['wallet_id'], $qp['last_question_id'], QuestState::fromId($qp['state']));
  }

  public function saveQuestProgress(IQuestProgress $questProgress): int
  {
    $sql = "INSERT INTO quest_progress (wallet_id, quest_id, score, completion_date, last_question_id, state)
              VALUES (:wallet_id, :quest_id, :score, :completion_date, :last_question_id, :state)";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([
      ':quest_id' => $questProgress->getQuestId(),
      ':wallet_id' => $questProgress->getWalletId(),
      ':score' => $questProgress->getScore(),
      ':completion_date' => $questProgress->getCompletionDate(),
      ':last_question_id' => $questProgress->getLastQuestionId(),
      ':state' => $questProgress->getState()->getStateId()
    ]);

    return (int) $this->db->connect()->lastInsertId();
  }

  public function updateQuestProgress(IQuestProgress $questProgress)
  {
    $sql = "UPDATE quest_progress
              SET score = :score,
                  completion_date = :completion_date,
                  last_question_id = :last_question_id,
                  state = :state
              WHERE wallet_id = :wallet_id
              AND quest_id = :quest_id";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([
      ':quest_id' => $questProgress->getQuestId(),
      ':wallet_id' => $questProgress->getWalletId(),
      ':score' => $questProgress->getScore(),
      ':completion_date' => $questProgress->getCompletionDate(),
      ':last_question_id' => $questProgress->getLastQuestionId(),
      ':state' => $questProgress->getState()->getStateId()
    ]);
  }
}