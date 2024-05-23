<?php

namespace App\Models;

use App\Models\QuestionType;

interface IQuestion
{
  public function getQuestionId(): int;
  public function getQuestId(): int;
  public function getText(): string;
  public function getType(): QuestionType;
  public function setQuestionId(int $id);
  public function getOptions(): array;
  public function __equals(IQuestion $other): bool;
}

