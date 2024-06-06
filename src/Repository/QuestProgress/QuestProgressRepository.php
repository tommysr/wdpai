<?php

namespace App\Repository\QuestProgress;

use App\Models\QuestProgress;
use App\Models\QuestState;
use App\Repository\Repository;
use App\Models\Interfaces\IQuestProgress;
use App\Repository\QuestProgress\IQuestProgressRepository;


class QuestProgressRepository extends Repository implements IQuestProgressRepository
{
  public function saveResponses(int $userId, array $optionsIds): void
  {
    $pdo = $this->db->connect();
    $sql = "INSERT INTO user_responses (user_id,  option_id)
              VALUES (:user_id, :option_id)";

    $stmt = $pdo->prepare($sql);
    foreach ($optionsIds as $option) {
      $stmt->execute([
        ':user_id' => $userId,
        ':option_id' => $option
      ]);
    }
  }

  public function getResponsesCount(int $optionId): int
  {
    $sql = "SELECT COUNT(*) as count FROM user_responses WHERE option_id = :option_id";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':option_id' => $optionId]);
    return (int) $stmt->fetchColumn();
  }

  public function getInProgress(int $userId): ?QuestProgress
  {
    $stateInProgress = QuestState::InProgress;
    $stateInt = $stateInProgress->getStateId();
    $sql = "SELECT qp.completion_date, qp.score, qp.quest_id, qp.wallet_id, qp.last_question_id, qp.state, w.address FROM quest_progress qp 
              INNER JOIN wallets w ON qp.wallet_id = w.wallet_id 
              INNER JOIN users u ON w.user_id = u.user_id 
              WHERE state = :state AND u.user_id = :user_id";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':state' => $stateInt, ':user_id' => $userId]);
    $qp = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$qp) {
      return null;
    }

    return new QuestProgress($qp['completion_date'], $qp['score'], $qp['quest_id'], $qp['wallet_id'], $qp['last_question_id'], QuestState::fromId($qp['state']), $qp['address']);
  }

  public function getQuestProgress(int $userId, int $questId): ?IQuestProgress
  {
    $sql = "SELECT qp.completion_date, qp.score, qp.quest_id, qp.wallet_id, qp.last_question_id, qp.state, w.address FROM quest_progress qp
              INNER JOIN wallets w ON qp.wallet_id = w.wallet_id
              INNER JOIN users u ON w.user_id = u.user_id
              WHERE u.user_id = :user_id AND qp.quest_id = :quest_id";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':quest_id' => $questId]);
    $qp = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$qp) {
      return null;
    }

    return new QuestProgress($qp['completion_date'], $qp['score'], $qp['quest_id'], $qp['wallet_id'], $qp['last_question_id'], QuestState::fromId($qp['state']), $qp['address']);  }

  public function saveQuestProgress(IQuestProgress $questProgress): void
  {
    $pdo = $this->db->connect();
    $sql = "INSERT INTO quest_progress (wallet_id, quest_id, score, completion_date, last_question_id, state)
              VALUES (:wallet_id, :quest_id, :score, :completion_date, :last_question_id, :state)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':quest_id' => $questProgress->getQuestId(),
      ':wallet_id' => $questProgress->getWalletId(),
      ':score' => $questProgress->getScore(),
      ':completion_date' => $questProgress->getCompletionDate(),
      ':last_question_id' => $questProgress->getLastQuestionId(),
      ':state' => $questProgress->getState()->getStateId()
    ]);
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

  public function getPercentileRank(int $userId, int $questId): int
  {
    $sql = "WITH total_participants AS (
              SELECT COUNT(*) AS total
              FROM public.quest_progress
              WHERE quest_id = :quest_id
            ),
            better_than_count AS (
              SELECT COUNT(*) AS better_than
              FROM public.quest_progress
              WHERE quest_id = :quest_id
                AND score < (
                    SELECT score
                    FROM public.quest_progress
                    INNER JOIN wallets ON quest_progress.wallet_id = wallets.wallet_id
                    WHERE wallets.user_id = :user_id AND quest_id = :quest_id 
                )
            )
            SELECT 
              CASE 
                  WHEN total = 0 THEN NULL
                  ELSE (better_than::decimal / total::decimal) * 100 
              END AS percentile_rank
            FROM 
                total_participants, better_than_count;";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId, ':quest_id' => $questId]);
    $qp = $stmt->fetchColumn();

    if (!$qp) {
      return 100;
    }

    return (int) $qp;
  }

  public function getUserEntries(int $userId): array
  {
    $sql = "SELECT qp.completion_date, qp.score, qp.quest_id, qp.wallet_id, qp.last_question_id, qp.state, w.address FROM quest_progress qp
              INNER JOIN wallets w ON qp.wallet_id = w.wallet_id
              INNER JOIN users u ON w.user_id = u.user_id
              WHERE u.user_id = :user_id";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    $entries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $progresses = [];
    foreach ($entries as $qp) {
      $progresses[] = new QuestProgress($qp['completion_date'], $qp['score'], $qp['quest_id'], $qp['wallet_id'], $qp['last_question_id'], QuestState::fromId($qp['state']), $qp['address']);
    }

    return $progresses;
  }
}