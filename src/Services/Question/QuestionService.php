<?php

namespace App\Services\Question;

use App\Models\QuestionType;
use App\Repository\IQuestionsRepository;
use App\Repository\IOptionsRepository;
use App\Models\IQuest;
use App\Models\IQuestion;
use App\Services\Question\IQuestionService;

class QuestionService implements IQuestionService
{
  private IQuestionsRepository $questionRepository;
  private IOptionsRepository $optionRepository;

  public function __construct(
    IQuestionsRepository $questionRepository,
    IOptionsRepository $optionRepository
  ) {
    $this->questionRepository = $questionRepository;
    $this->optionRepository = $optionRepository;
  }

  public function updateQuestions(IQuest $quest): void
  {
    $questId = $quest->getQuestID();
    foreach ($quest->getQuestions() as $question) {
      $question->setQuestId($questId);
      $this->processQuestion($question);
    }
  }

  private function processQuestion(IQuestion $question): void
  {
    switch ($question->getFlag()) {
      case 'added':
        $questionId = $this->questionRepository->saveQuestion($question);
        $question->setQuestionId($questionId);
        $this->processOptions($question);
        break;
      case 'removed':
        $this->optionRepository->deleteAllOptions($question->getQuestionId());
        $this->questionRepository->deleteQuestionById($question->getQuestionId());
        break;
      default:
        $this->questionRepository->updateQuestions([$question]);
        $this->processOptions($question);
    }
  }

  private function processOptions(IQuestion $question): void
  {
    foreach ($question->getOptions() as $option) {
      switch ($option->getFlag()) {
        case 'added':
          $option->setQuestionId($question->getQuestionId());
          $this->optionRepository->saveOption($option);
          break;
        case 'removed':
          $this->optionRepository->deleteOptionById($option->getOptionId());
          break;
        default:
          $this->optionRepository->updateOptions([$option]);
      }
    }
  }

  public function fetchQuestions(IQuest $quest): array
  {
    $questions = $this->questionRepository->getQuestionsByQuestId($quest->getQuestID());

    foreach ($questions as $question) {
      $options = $this->optionRepository->getOptionsByQuestionId($question->getQuestionId());
      $question->setOptions($options);
    }

    return $questions;
  }

  public function getQuestionWithOptions(int $questionId): ?IQuestion
  {
    $question = $this->questionRepository->getById($questionId);

    if (!$question) {
      return null;
    }

    $options = $this->optionRepository->getOptionsByQuestionId($questionId);
    $question->setOptions($options);
    return $question;
  }


  public function evaluateOptions(int $questionId, array $selectedOptions): array
  {
    $question = $this->questionRepository->getById($questionId);

    if ($question->getType() === QuestionType::READ_TEXT) {
      return ['points' => $question->getPoints(), 'options' => [], 'maxPoints' => $question->getPoints()];
    }

    $options = $this->optionRepository->getOptionsByQuestionId($questionId);
    $optionIds = array_map(fn($option) => $option->getOptionId(), $options);
    $correctIds = $this->optionRepository->getCorrectOptionsIdsForQuestionId($questionId);
    $correctCount = count($correctIds);
    $chosenCount = count(array_intersect($correctIds, $selectedOptions));

    if ($question->getType() === QuestionType::SINGLE_CHOICE && $chosenCount != 1) {
      throw new \Exception("Single choice question must have exactly one option selected");
    }

    if ($question->getType() === QuestionType::MULTIPLE_CHOICE && $chosenCount < 1) {
      throw new \Exception("Multiple choice question must have at least one option selected");
    }

    $points = $question->getPoints();

    if ($correctCount != 0) {
      $points = round(($chosenCount / $correctCount) * $question->getPoints());
    }
    return [
      'points' => $points,
      'maxPoints' => $question->getPoints(),
      'options' => array_intersect($correctIds, $optionIds)
    ];
  }
}
