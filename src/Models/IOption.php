<?php

namespace App\Models;

interface IOption
{
  public function getOptionId(): int;
  public function setOptionId(int $id);
  public function getQuestionId(): int;
  public function getText(): string;
  public function getIsCorrect(): bool;
  public function __equals(Option $other): bool;
}