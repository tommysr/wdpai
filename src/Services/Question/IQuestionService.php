<?php

namespace App\Services\Question;

use App\Models\IQuest;
use App\Models\IQuestion;


interface IQuestionService
{
  public function fetchQuestions(IQuest $quest): array;
  public function updateQuestions(IQuest $quest): void;
  public function getQuestionWithOptions(int $questionId): ?IQuestion;
  public function evaluateOptions(int $questionId, array $selectedOptions): array;
}
