<?php

require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../repository/QuestionsRepository.php';
require_once __DIR__ . '/../repository/OptionsRepository.php';
require_once __DIR__ . '/../exceptions/Quests.php';
require_once __DIR__ . '/../models/Quest.php';



class QuestService
{
  private $questRepository;
  private $questionRepository;
  private $optionRepository;


  public function __construct($questRepository = null, $questionRepository = null, $optionRepository = null)
  {
    $this->questRepository = $questRepository ?: new QuestRepository();
    $this->questionRepository = $questionRepository ?: new QuestionsRepository();
    $this->optionRepository = $optionRepository ?: new OptionsRepository();
  }

  public function getQuestWithQuestionsAndOptions(?int $questId = null): ?Quest
  {
    if ($questId === null) {
      return null;
    }

    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null) {
      throw new NotFoundException('Quest not found.');
    }

    $questions = $this->questionRepository->getQuestionsByQuestId($questId);

    foreach ($questions as $question) {
      $options = $this->optionRepository->getOptionsByQuestionId($question->getQuestionId());
      $question->setOptions($options);
    }

    $quest->setQuestions($questions);

    return $quest;
  }
}
