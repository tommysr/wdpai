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

    $questId = isset($data['questId']) ? intval($data['questId']) : 0;
    $this->questBuilder->setQuestId($questId);

    $title = htmlspecialchars($data['title'], ENT_QUOTES);
    $this->questBuilder->setTitle($title);

    $description = htmlspecialchars($data['description'], ENT_QUOTES);
    $this->questBuilder->setDescription($description);

    $blockchain = htmlspecialchars($data['blockchain'], ENT_QUOTES);
    $this->questBuilder->setBlockchain($blockchain);

    $requiredMinutes = intval($data['minutesRequired']);
    $this->questBuilder->setRequiredMinutes($requiredMinutes);

    $expiryDate = htmlspecialchars($data['expiryDate'], ENT_QUOTES);
    $this->questBuilder->setExpiryDateString($expiryDate);

    $payoutDate = htmlspecialchars($data['payoutDate'], ENT_QUOTES);
    $this->questBuilder->setPayoutDate($payoutDate);

    $participantsLimit = intval($data['participantsLimit']);
    $this->questBuilder->setParticipantsLimit($participantsLimit);

    $poolAmount = floatval($data['poolAmount']);
    $this->questBuilder->setPoolAmount($poolAmount);

    $token = htmlspecialchars($data['token'], ENT_QUOTES);
    $this->questBuilder->setToken($token);
    $questThumbnail = htmlspecialchars($data['questThumbnail'], ENT_QUOTES);
    $this->questBuilder->setPictureUrl($questThumbnail);

    if (isset($data['creatorId'])) {
      $this->questBuilder->setCreatorId(intval($data['creatorId']));
    }

    $this->questBuilder->setIsApproved(false);

    if (isset($data['questions'])) {
      foreach ($data['questions'] as $questionData) {
        $questionText = htmlspecialchars($questionData['text'], ENT_QUOTES);
        $questionFlag = isset($questionData['flag']) ? htmlspecialchars($questionData['flag'], ENT_QUOTES) : null;
        $score = intval($questionData['score']);
        $questionId = isset($questionData['id']) ? intval($questionData['id']) : 0;

        $question = new Question(
          $questionId,
          $questId,
          $questionText,
          QuestionType::UNKNOWN->value,
          $score,
          $questionFlag
        );

        $correctOptionsCount = 0;

        if (isset($questionData['options'])) {
          foreach ($questionData['options'] as $optionData) {
            $optionId = isset($optionData['id']) ? intval($optionData['id']) : 0;
            $optionText = htmlspecialchars($optionData['text'], ENT_QUOTES);
            $optionFlag = isset($optionData['flag']) ? htmlspecialchars($optionData['flag'], ENT_QUOTES) : null;
            $isCorrect = isset($optionData['isCorrect']) ? true : false;

            $option = new Option(
              $optionId,
              $question->getQuestionId(),
              $optionText,
              $isCorrect,
              $optionFlag
            );

            if ($isCorrect) {
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