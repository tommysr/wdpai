<?php

namespace App\Services\Quests;

use App\Repository\IWalletRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Quest\IQuestProvider;
use App\Services\Question\IQuestionService;
use App\Repository\IQuestRepository;
use App\Models\IQuest;

class QuestProvider implements IQuestProvider
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
    $this->questionService = $questionService;
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

  public function getQuest(int $id): ?IQuest
  {
    return $this->questRepository->getQuestById($id);
  }
}