<?php

namespace App\Services\Quests\Builder;


use App\Models\IQuest;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\QuestionTypeUtil;
use App\Services\Quests\Builder\IQuestBuilderService;


class QuestBuilderService implements IQuestBuilderService
{
  private IQuestBuilder $questBuilder;

  public function __construct(IQuestBuilder $questBuilder)
  {
    $this->questBuilder = $questBuilder ?: new QuestBuilder();
  }

  public function setBuilder(IQuestBuilder $questBuilder): void
  {
    $this->questBuilder = $questBuilder;
  }

  public function buildQuest(array $data): IQuest
  {
    $this->questBuilder->reset();

    if (isset($data['questId'])) {
      $this->questBuilder->setQuestId($data['questId']);
    }

    $this->questBuilder->setTitle($data['title']);
    $this->questBuilder->setDescription($data['description']);
    $this->questBuilder->setWorthKnowledge($data['worthKnowledge']);
    $this->questBuilder->setRequiredWallet($data['requiredWallet']);
    $this->questBuilder->setRequiredMinutes($data['timeRequired']);
    $this->questBuilder->setExpiryDateString($data['expiryDate']);
    $this->questBuilder->setParticipantsCount(0);
    $this->questBuilder->setParticipantsLimit($data['participantLimit']);
    $this->questBuilder->setPoolAmount($data['poolAmount']);
    $this->questBuilder->setToken($data['token']);

    if (isset($data['questId'])) {
      $this->questBuilder->setCreatorId($data['creatorId']);
    }

    $points = 0;

    $this->questBuilder->setIsApproved(false);

    foreach ($data['questions'] as $questionData) {
      $question = new Question(
        0,
        0,
        $questionData['text'],
        $questionData['score'],
        isset($questionData['flag']) ? $questionData['flag'] : null
      );

      $points += $question->getPoints();
      $correctOptionsCount = 0;

      foreach ($questionData['options'] as $optionData) {
        $option = new Option(
          0,
          0,
          $optionData['text'],
          $optionData['isCorrect'],
          isset($questionData['flag']) ? $questionData['flag'] : null
        );
        if ($optionData['isCorrect']) {
          $correctOptionsCount++;
        }
        $question->addOption($option);
      }

      if ($correctOptionsCount === 1) {
        $question->setType(QuestionType::SINGLE_CHOICE);
      } else if ($correctOptionsCount > 1) {
        $question->setType(QuestionType::MULTIPLE_CHOICE);
      } else {
        $question->setType(QuestionType::READ_TEXT);
      }

      $this->questBuilder->addQuestion($question);
    }

    $this->questBuilder->setMaxPoints($points);

    return $this->questBuilder->build();
  }
}