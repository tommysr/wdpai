<?php

namespace App\Models;

use App\Models\QuestionType;

interface IQuestion
{
  public function setFlag(string $flag): void;
  public function getFlag(): ?string;
  public function getPoints(): int;
  public function getQuestionId(): int;
  public function getQuestId(): int;
  public function getText(): string;
  public function getType(): string;
  public function setType(string $type): void;
  public function getOptions(): array;
  public function addOption(IOption $option): void;
  public function setOptions(array $options): void;
  public function setQuestId(int $questId): void;
  public function setQuestionId(int $questionId): void;
}

