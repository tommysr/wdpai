<?php

namespace App\Services\Question;

use App\Models\IQuest;
use App\Models\IQuestion;


interface IQuestionService
{
  public function fetchQuestions(IQuest $quest): array;
  public function processQuestions(IQuest $quest): void;
  private function processQuestion(IQuestion $question): void;
  private function processOptions(IQuestion $question): void;
}
