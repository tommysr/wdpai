<?php

namespace App\Repository;

use App\Models\IOption;

interface IOptionsRepository
{
  public function getCorrectOptionsIdsForQuestionId(int $questionId): array;
  public function getOptionsByQuestionId(int $questionId): array;
  public function updateOptions(array $options): void;
  public function deleteOptions(array $options): void;
  public function saveNewOptions(int $questionId, array $options): void;
  public function saveOptions(array $options): void;
  public function deleteAllOptions(int $questionId): void;
}