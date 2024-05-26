<?php

namespace App\Models;

use App\Models\QuestionType;

interface IQuestion
{
  public function setFlag(string $flag): void;

  public function getFlag(): ?string;
  public function getQuestionId(): int;
  public function getQuestId(): int;
  public function getText(): string;
  public function getType(): QuestionType;
  public function setQuestionId(int $id);
  public function getOptions(): array;
  public function addOption(IOption $option):void;
  public function __equals(IQuestion $other): bool;
}

