<?php

namespace App\Services\Quests;

use App\Repository\IOptionsRepository;
use App\Repository\IWalletRepository;
use App\Result\IResult;
use App\Result\Result;
use App\Services\Authenticate\IIdentity;
use App\Services\Question\IQuestionService;
use App\Services\Quests\IQuestService;
use App\Repository\IQuestRepository;
use App\Repository\IQuestionsRepository;
use App\Models\IQuest;


class QuestService implements IQuestService
{
  private IQuestRepository $questRepository;
  private IQuestionService $questionService;
  private IWalletRepository $walletRepository;

  public function __construct(
    IQuestRepository $questRepository,
    IQuestionService $questionService,
    IWalletRepository $walletRepository
  ) {
    $this->questRepository = $questRepository;
    $this->questionRepository = $questionService;
    $this->walletRepository = $walletRepository;
  }

  public function getQuestsToApproval(): array
  {
    return $this->questRepository->getQuestToApprove();
  }

  public function getApprovedQuests(): array
  {
    return $this->questRepository->getApprovedQuests();
  }

  public function getQuestsByIds(array $questIds): array
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
  }

  public function editQuest(IQuest $quest): void
  {
    $this->questRepository->updateQuest($quest);
    $this->questionService->updateQuestions($quest);
  }

  public function createQuest(IQuest $quest): void
  {
    $questId = $this->questRepository->saveQuest($quest);
    $quest->setQuestID($questId);
    $this->questionService->updateQuestions($quest);
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

    if ($quest) {
      return null;
    }

    $questions = $this->questionService->fetchQuestions($quest);
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