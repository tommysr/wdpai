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
    } else {
      $this->questBuilder->setQuestId(0);
    }

    $this->questBuilder->setTitle($data['title']);
    $this->questBuilder->setDescription($data['description']);
    $this->questBuilder->setBlockchain($data['blockchain']);
    $this->questBuilder->setRequiredMinutes($data['minutesRequired']);
    $this->questBuilder->setExpiryDateString($data['expiryDate']);
    $this->questBuilder->setPayoutDate($data['payoutDate']);
    $this->questBuilder->setParticipantsLimit($data['participantsLimit']);
    $this->questBuilder->setPoolAmount($data['poolAmount']);
    $this->questBuilder->setToken($data['token']);

    if (isset($data['creatorId'])) {
      $this->questBuilder->setCreatorId($data['creatorId']);
    }


    $this->questBuilder->setIsApproved(false);

    if (isset($data['questions'])) {
      foreach ($data['questions'] as $questionData) {
        $question = new Question(
          $questionData['id'] ?? 0,
          $data['id'] ?? 0,
          $questionData['text'],
          QuestionType::UNKNOWN->value,
          $questionData['score'],
          isset($questionData['flag']) ? $questionData['flag'] : null
        );

        $correctOptionsCount = 0;

        if (isset($questionData['options'])) {
          foreach ($questionData['options'] as $optionData) {
            $option = new Option(
              $optionData['id'] ?? 0,
              $question->getQuestionId(),
              $optionData['text'],
              isset($optionData['isCorrect']) ? true : false,
              isset($optionData['flag']) ? $optionData['flag'] : null
            );
            if (isset($optionData['isCorrect'])) {
              $correctOptionsCount++;
            }
            $question->addOption($option);
          }
        }

        if ($correctOptionsCount === 1) {
          $question->setType(QuestionType::SINGLE_CHOICE->value);
        } else if ($correctOptionsCount > 1) {
          $question->setType(QuestionType::MULTIPLE_CHOICE->value);
        } else {
          $question->setType(QuestionType::READ_TEXT->value);
        }

        $this->questBuilder->addQuestion($question);
      }
    }



    return $this->questBuilder->build();
  }
}