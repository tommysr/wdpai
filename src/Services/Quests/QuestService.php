<?php

namespace App\Services\Quests;

use App\Repository\IOptionsRepository;
use App\Repository\IWalletRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestService;
use App\Repository\IQuestRepository;
use App\Repository\IQuestionsRepository;
use App\Models\IQuest;


class QuestService implements IQuestService
{
  private IQuestRepository $questRepository;
  private IQuestionsRepository $questionRepository;
  private IOptionsRepository $optionRepository;
  private IWalletRepository $walletRepository;

  public function __construct(
    IQuestRepository $questRepository,
    IQuestionsRepository $questionRepository,
    IOptionsRepository $optionRepository,
    IWalletRepository $walletRepository
  ) {
    $this->questRepository = $questRepository;
    $this->questionRepository = $questionRepository;
    $this->optionRepository = $optionRepository;
    $this->walletRepository = $walletRepository;
  }

  public function getQuestsToApproval(): array
  {
    $quests = $this->questRepository->getQuests();


    return array_filter($quests, function ($quest) {
      return !$quest->getIsApproved();
    });
  }


  public function getApprovedQuests(): array
  {
    return $this->questRepository->getApprovedQuests();
  }


  public function getQuests(array $questIds): array
  {
    $quests = [];
    foreach ($questIds as $questId) {
      $quest = $this->questRepository->getQuestById($questId);
      $quests[] = $quest;
    }
    return $quests;
  }

  public function getQuestsToPlay(): array
  {
    $quests = $this->questRepository->getApprovedQuests();

    return array_filter($quests, function ($quest) {
      return $quest->getParticipantsCount() < $quest->getParticipantsLimit() &&
        \DateTime::createFromFormat('Y-m-d', $quest->getExpiryDateString()) > new \DateTime();
    });
  }

  public function getTopRatedQuests(): array
  {
    $quests = $this->questRepository->getApprovedQuests();

    usort($quests, function ($a, $b) {
      return $b->getAvgRating() <=> $a->getAvgRating();
    });

    return $quests;
  }

  public function getCreatorQuests(IIdentity $identity): array
  {
    $creatorId = $identity->getId();
    return $this->questRepository->getCreatorQuests($creatorId);

    // return array_filter($quests, function ($quest) {
    //   return !$quest->getIsApproved();
    // });
  }

  public function getQuestBlockchain(int $questId): string
  {
    $quest = $this->questRepository->getQuestById($questId);

    if (!$quest) {
      throw new \Exception('Quest not found');
    }

    return $quest->getBlockchain();
  }

  public function editQuest(IQuest $quest): IQuestResult
  {
    // Update quest without flag
    $this->questRepository->updateQuest($quest);

    // Handle questions and options
    foreach ($quest->getQuestions() as $question) {
      switch ($question->getFlag()) {
        case 'added':
          $question->setQuestId($quest->getQuestID());
          $questionId = $this->questionRepository->saveQuestion($question);

          foreach ($question->getOptions() as $option) {
            $option->setQuestionId($questionId);
            $this->optionRepository->saveOption($option);
          }
          break;
        case 'removed':

          $this->optionRepository->deleteAllOptions($question->getQuestionId());
          $this->questionRepository->deleteQuestionById($question->getQuestionId());
          break;
        default:
          $this->questionRepository->updateQuestions([$question]);

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
    }
    return new QuestResult([], [], true);
  }

  public function createQuest(IQuest $quest): IQuestResult
  {
    $questId = $this->questRepository->saveQuest($quest);
    $questions = $quest->getQuestions();

    // Handle questions and options
    foreach ($questions as $question) {
      $question->setQuestId($questId);
      $questionId = $this->questionRepository->saveQuestion($question);

      foreach ($question->getOptions() as $option) {
        $option->setQuestionId($questionId);
        $this->optionRepository->saveOption($option);
      }
    }

    return new QuestResult([], [], true);
  }

  public function addParticipant(int $questId): bool
  {
    $quest = $this->questRepository->getQuestById($questId);

    if ($quest === null) {
      return false;
    }

    $participantsCount = $quest->getParticipantsCount();

    if ($participantsCount >= $quest->getParticipantsLimit()) {
      return false;
    }

    $quest->setParticipantsCount($participantsCount + 1);
    $this->questRepository->updateQuest($quest);

    return true;
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
    $this->questRepository->changeApproved($questId, true);
  }

  public function unpublishQuest(int $questId): void
  {
    $this->questRepository->changeApproved($questId, false);
  }

  public function getQuest(int $id): ?IQuest
  {
    return $this->questRepository->getQuestById($id);
  }
}