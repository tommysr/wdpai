<?php

class Option
{
  private int $optionId;
  private int $questionId;
  private string $text;
  private bool $isCorrect;


  public function __construct(int $optionId, int $questionId, string $text, bool $isCorrect)
  {
    $this->optionId = $optionId;
    $this->questionId = $questionId;
    $this->text = $text;
    $this->isCorrect = $isCorrect;
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