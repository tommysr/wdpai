<?php

namespace App\Models;

use App\Models\QuestionType;
use App\Models\IQuestion;


class Question implements IQuestion
{
  private ?string $flag = null;
  private int $questionId;
  private int $questId;
  private string $text;
  private QuestionType $type;
  private int $score;

  private $options = array();



  public function __construct(int $questionId, int $questId, string $text, string $type, int $score, string $flag = null)
  {
    $this->questionId = $questionId;
    $this->questId = $questId;
    $this->text = $text;
    $this->type = QuestionType::from($type);
    $this->score = $score;
    $this->flag = $flag;
  }

  public function addOption(IOption $option): void
  {
    $this->options[] = $option;
  }

  public function setFlag(string $flag): void
  {
    $this->flag = $flag;
  }

  public function getFlag(): string|null
  {
    return $this->flag;
  }

  public function setQuestionId(int $id)
  {
    $this->questionId = $id;
  }

  public function getQuestionId(): int
  {
    return $this->questionId;
  }

  public function setQuestId(int $questId)
  {
    $this->questId = $questId;
  }

  public function getQuestId(): int
  {
    return $this->questId;
  }

  public function getText(): string
  {
    return $this->text;
  }

  public function getType(): string
  {
    return $this->type->value;
  }

  public function getOptions(): array
  {
    return $this->options;
  }

  public function getPoints(): int
  {
    return $this->score;
  }

  public function setType(string $type): void
  {
    $this->type = QuestionType::from($type);
  }
}