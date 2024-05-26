<?php

namespace App\Models;

use App\Models\IOption;

class Option implements IOption
{
  private ?string $flag = null;
  private int $optionId;
  private int $questionId;
  private string $text;
  private bool $isCorrect;

  public function __equals(IOption $other): bool
  {
    return $this->optionId === $other->getOptionId()
      && $this->questionId === $other->getQuestionId()
      && $this->text === $other->getText()
      && $this->isCorrect === $other->getIsCorrect();
  }

  public function __construct(int $optionId, int $questionId, string $text, bool $isCorrect, string $flag = null)
  {
    $this->optionId = $optionId;
    $this->questionId = $questionId;
    $this->text = $text;
    $this->isCorrect = $isCorrect;
    $this->flag = $flag;
  }

  public function setQuestionId(int $id)
  {
    $this->questionId = $id;
  }

  public function setFlag(string $flag): void
  {
    $this->flag = $flag;
  }

  public function getFlag(): string|null
  {
    return $this->flag;
  }

  public function setOptionId(int $id)
  {
    $this->optionId = $id;
  }

  public function getQuestionId(): int
  {
    return $this->questionId;
  }

  public function getOptionId(): int
  {
    return $this->optionId;
  }

  public function getText(): string
  {
    return $this->text;
  }

  public function getIsCorrect(): bool
  {
    return $this->isCorrect;
  }
}