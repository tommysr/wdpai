<?php

namespace App\Services\Question;

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

  public function processQuestions(IQuest $quest): void
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
        $question->setQuestionId($question->getQuestionId());
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
}
