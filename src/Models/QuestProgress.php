<?php

namespace App\Models;

use App\Models\Interfaces\IQuestProgress;
use App\Models\Interfaces\IQuestState;


class QuestProgress implements IQuestProgress
{
  private string|null $completion_date;
  private int $score;
  private int $quest_id;
  private int $wallet_id;
  private int $last_question_id;
  private IQuestState $state;


  static function fromArray(array $data): IQuestProgress
  {
    return new QuestProgress(
      $data['completion_date'],
      $data['score'],
      $data['quest_id'],
      $data['wallet_id'],
      $data['last_question_id'],
      QuestState::from($data['state'])
    );
  }

  public function __construct(string|null $completion_date, int $score, int $quest_id, int $wallet_id, int $last_question_id, IQuestState $state)
  {
    $this->completion_date = $completion_date;
    $this->score = $score;
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

  public function getQuestId(): int
  {
    return $this->quest_id;
  }

  public function getWalletId(): int
  {
    return $this->wallet_id;
  }

  public function getState(): IQuestState
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

  public function setScore(int $score): void
  {
    $this->score = $score;
  }

  public function isCompleted(): bool
  {
    return $this->state !== QuestState::InProgress;
  }

  public function setState(IQuestState $state): void
  {
    $this->state = $state;
  }

  public function setLastQuestionId(int $last_question_id)
  {
    $this->last_question_id = $last_question_id;
  }

  public function setNextQuestionId(int $questionId): void
  {
    $this->last_question_id = $questionId;
  }

  public function setCompletionDateToNow(): void
  {
    $this->completion_date = date('Y-m-d H:i:s');
  }
}
