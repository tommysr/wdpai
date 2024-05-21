<?php

namespace App\Models;

use App\Models\IQuestStatistics;

class QuestStateMachine
{
  const STATE_IN_PROGRESS = 'STATE_IN_PROGRESS';
  const STATE_COMPLETED = 'STATE_COMPLETED';
  const STATE_ABANDONED = 'STATE_ABANDONED';

  // Transition rules
  private $transitions = [
    self::STATE_IN_PROGRESS => [
      'complete' => self::STATE_COMPLETED,
      'abandon' => self::STATE_ABANDONED
    ],
    self::STATE_COMPLETED => [],
    self::STATE_ABANDONED => []
  ];

  public function transition(string $currentState, string $action): string
  {
    if (!isset($this->transitions[$currentState][$action])) {
      throw new \Exception("Invalid transition from state '{$currentState}' with action '{$action}'");
    }
    return $this->transitions[$currentState][$action];
  }
}

class QuestStatistics implements IQuestStatistics
{
  private string|null $completion_date;
  private int $score;
  private int $user_id;
  private int $quest_id;
  private int $wallet_id;
  private int $last_question_id;
  private string $state;


  static function fromArray(array $data): QuestStatistics
  {
    return new QuestStatistics(
      $data['completion_date'],
      $data['score'],
      $data['user_id'],
      $data['quest_id'],
      $data['wallet_id'],
      $data['last_question_id'],
      $data['state']
    );
  }

  public function __construct(string|null $completion_date, int $score, int $user_id, int $quest_id, int $wallet_id, int $last_question_id, string $state)
  {
    $this->completion_date = $completion_date;
    $this->score = $score;
    $this->user_id = $user_id;
    $this->quest_id = $quest_id;
    $this->wallet_id = $wallet_id;
    $this->last_question_id = $last_question_id;
    $this->state = $state;
  }

  public function getCompletionDate(): ?string
  {
    return $this->completion_date;
  }

  public function getScore(): int
  {
    return $this->score;
  }

  public function getUserId(): int
  {
    return $this->user_id;
  }

  public function getQuestId(): int
  {
    return $this->quest_id;
  }


  public function getWalletId(): int
  {
    return $this->wallet_id;
  }

  public function getState(): string
  {
    return $this->state;
  }


  public function getLastQuestionId(): int
  {
    return $this->last_question_id;
  }

  public function setCompletionDate(string $completion_date)
  {
    $this->completion_date = $completion_date;
  }

  public function setScore(int $score)
  {
    $this->score = $score;
  }
}
