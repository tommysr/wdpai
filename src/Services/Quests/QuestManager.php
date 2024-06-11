<?php

namespace App\Services\Quests;

use App\Services\Question\IQuestionService;
use App\Repository\IQuestRepository;
use App\Models\IQuest;


class QuestManager implements IQuestManager
{
  private IQuestRepository $questRepository;
  private IQuestionService $questionService;

  public function __construct(
    IQuestRepository $questRepository,
    IQuestionService $questionService,
  ) {
    $this->questRepository = $questRepository;
    $this->questionService = $questionService;
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

  public function publishQuest(int $questId): void
  {
    $this->questRepository->changeApproved($questId, true);
  }

  public function unpublishQuest(int $questId): void
  {
    $this->questRepository->changeApproved($questId, false);
  }
}
