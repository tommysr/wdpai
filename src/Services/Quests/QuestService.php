<?php

namespace App\Services\Quests;

use App\Models\IOption;
use App\Models\IQuestion;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\QuestionTypeUtil;
use App\Repository\IOptionsRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestService;
use App\Repository\IQuestRepository;
use App\Repository\IQuestionsRepository;
use App\Models\IQuest;
use App\Models\Quest;
use App\Repository\OptionsRepository;
use App\Repository\QuestRepository;
use App\Repository\QuestionsRepository;
use App\Validator\IValidationChain;
use App\Validator\Quest\QuestValidationChain;

class QuestService implements IQuestService
{
  private IQuestRepository $questRepository;
  private IQuestionsRepository $questionRepository;
  private IOptionsRepository $optionRepository;
  private IValidationChain $validationChain;

  public function __construct(
    IQuestRepository $questRepository = null,
    IQuestionsRepository $questionRepository = null,
    IOptionsRepository $optionRepository = null,
    IValidationChain $validationChain = null
  ) {
    $this->questRepository = $questRepository ?: new QuestRepository();
    $this->questionRepository = $questionRepository ?: new QuestionsRepository();
    $this->optionRepository = $optionRepository ?: new OptionsRepository();
    $this->validationChain = $validationChain ?: new QuestValidationChain();
  }


  public function getQuestsToApproval(): array
  {
    $quests = $this->questRepository->getQuests();

    array_filter($quests, function ($quest) {
      return $quest->getIsApproved();
    });

    return $quests;
  }

  public function getQuestsToPlay(): array
  {
    $quests = $this->questRepository->getApprovedQuests();

    array_filter($quests, function ($quest) {
      return $quest->getParticipantsCount() < $quest->getParticipantLimit() &&
        $quest->getExpiryDateString() > date('Y-m-d H:i:s', time());
    });

    return $quests;
  }


  public function getCreatorQuests(IIdentity $identity): array
  {
    $quests = $this->questRepository->getCreatorQuests($identity->getId());

    array_filter($quests, function ($quest) {
      return !$quest->getIsApproved();
    });

    return $quests;
  }

  public function getQuests(string $role): array
  {
    if ($role === 'admin') {
      return $this->getAllQuests();
    } else if ($role === 'user') {
      return $this->getUserQuests();
    } else if ($role === 'creator') {
      return $this->getAllQuests();
    }

    return [];
  }


  private function getAllQuests(): array
  {
    return $this->questRepository->getQuests();
  }

  private function getAdminQuests(): array
  {
    return $this->questRepository->getQuests();
  }

  private function getUserQuests(): array
  {
    return $this->questRepository->getApprovedQuests();
  }

  private function validateQuestData(array $data): array
  {
    return $this->validationChain->validateFields($data);
  }

  private function saveQuestion(array $data, int $questId): IQuestion
  {
    $question = new Question(
      0,
      $questId,
      $data['text'],
      QuestionTypeUtil::fromString($data['type']),
      $data['score'],
    );

    $id = $this->questionRepository->saveQuestion($question);

    $question->setQuestionId($id);

    return $question;
  }

  private function updateQuestFromData(array $data, int $creatorId, int $questId)
  {
    $quest = new Quest(
      $questId,
      $data['title'],
      $data['description'],
      $data['worthKnowledge'],
      $data['requiredWallet'],
      $data['timeRequired'],
      $data['expiryDate'],
      0,
      $data['participantsLimit'],
      $data['poolAmount'],
      $data['token'],
      $data['points'],
      $creatorId,
      false
    );

    $this->questRepository->updateQuest($quest);
  }

  private function saveQuest(array $data, int $creatorId): int
  {
    $quest = new Quest(
      0,
      $data['title'],
      $data['description'],
      $data['worthKnowledge'],
      $data['requiredWallet'],
      $data['timeRequired'],
      $data['expiryDate'],
      0,
      $data['participantsLimit'],
      $data['poolAmount'],
      $data['token'],
      $data['points'],
      $creatorId,
      false
    );

    return $this->questRepository->saveQuest($quest);
  }

  private function saveOption(array $data, int $questionId): IOption
  {
    $option = new Option(
      0,
      $questionId,
      $data['text'],
      $data['isCorrect']
    );

    $id = $this->optionRepository->saveOption($option);

    $option->setOptionId($id);

    return $option;
  }

  private function updateQuestionFromData(array $data)
  {
    $question = new Question(
      $data['questionId'],
      $data['questId'],
      $data['text'],
      QuestionTypeUtil::fromString($data['type']),
      $data['score'],
    );

    $this->questionRepository->updateQuestions([$question]);
  }

  private function updateOptionsFromData(array $options, int $questionId)
  {
    foreach ($options as $option) {
      if (!$option) {
        continue;
      }

      switch ($option['flag']) {
        case 'added':
          $this->saveOption($option, $questionId);
          break;
        case 'deleted':
          $this->optionRepository->deleteOptionById($option['id']);
          break;
        default:
          $option = new Option(
            $option['id'],
            $questionId,
            $option['text'],
            $option['isCorrect']
          );

          $this->optionRepository->updateOptions([$option]);
      }
    }
  }

  public function editQuest(array $data, int $creatorId, int $questId): IQuestResult
  {
    $errors = $this->validateQuestData($data);

    if (!empty($errors)) {
      return new QuestResult($errors);
    }

    $this->updateQuestFromData($data, $creatorId, $questId);

    $questions = $data['questions'];

    if (empty($questions)) {
      return new QuestResult(['Questions are required.']);
    }


    foreach ($questions as $question) {
      if (!$question) {
        continue;
      }

      switch ($question['flag']) {
        case 'added':
          $question = $this->saveQuestion($question, $questId);

          $options = $question['options'];

          if ($question->getType() != QuestionType::READ_TEXT && empty($options)) {
            return new QuestResult(['Options are required.']);
          }

          foreach ($options as $option) {
            if (!$option) {
              continue;
            }

            $this->saveOption($option, $question->getQuestionId());
          }

          break;
        case 'deleted':
          $id = $question['id'];
          $this->questionRepository->deleteQuestionById($id);
          $this->optionRepository->deleteAllOptions($id);
          break;
        default:
          $this->updateQuestionFromData($question);
          $options = $question['options'];
          if (!empty($options)) {
            $this->updateOptionsFromData($options, $question['id']);
          }
      }
    }

    return new QuestResult([], [], true);
  }

  public function createQuest(array $data, int $creatorId): IQuestResult
  {
    $errors = $this->validateQuestData($data);

    if (!empty($errors)) {
      return new QuestResult($errors);
    }

    $questId = $this->saveQuest($data, $creatorId);

    $questions = $data['questions'];

    if (empty($questions)) {
      return new QuestResult(['Questions are required.']);
    }

    foreach ($questions as $question) {
      if (!$question) {
        continue;
      }

      $question = $this->saveQuestion($question, $questId);

      $options = $question['options'];

      if ($question->getType() != QuestionType::READ_TEXT && empty($options)) {
        return new QuestResult(['Options are required.']);
      }

      foreach ($options as $option) {
        if (!$option) {
          continue;
        }

        $this->saveOption($option, $question->getQuestionId());
      }
    }

    return new QuestResult([], [], true);
  }

  public function getQuestWithQuestions(int $questId): ?IQuest
  {
    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null) {
      return null;
    }

    $questions = $this->questionRepository->getQuestionsByQuestId($questId);

    foreach ($questions as $question) {
      $options = $this->optionRepository->getOptionsByQuestionId($question->getQuestionId());
      $question->setOptions($options);
    }

    $quest->setQuestions($questions);

    return $quest;
  }

  public function publishQuest(int $questId): void
  {
    $this->questRepository->approve($questId);
  }

  public function getQuest(int $id): ?IQuest
  {
    return $this->questRepository->getQuestById($id);
  }
}