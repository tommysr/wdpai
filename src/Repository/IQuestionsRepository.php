<?php

namespace App\Repository;
use App\Models\IQuestion;

interface IQuestionsRepository
{
  public function getById(int $questionId): ?IQuestion;
  public function getQuestionsByQuestId(int $questId);
  public function deleteQuestions(array $questions): void;
  public function deleteAllQuestions(int $questId): void;
  public function updateQuestions(array $questions): void;
  public function saveQuestion(IQuestion $question): int;
  public function saveQuestions(array $questions): void;
}
