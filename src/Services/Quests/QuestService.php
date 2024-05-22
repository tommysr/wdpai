<?php

namespace App\Services\Quests;

use App\Repository\IOptionsRepository;
use App\Services\Authenticate\IIdentity;
use App\Services\Quests\IQuestService;
use App\Repository\IQuestRepository;
use App\Repository\IQuestionsRepository;
use App\Models\IQuest;
use App\Repository\OptionsRepository;
use App\Repository\QuestRepository;
use App\Repository\QuestionsRepository;

class QuestService implements IQuestService
{
  private IQuestRepository $questRepository;
  private IQuestionsRepository $questionRepository;
  private IOptionsRepository $optionRepository;

  public function __construct(
    IQuestRepository $questRepository = null,
    IQuestionsRepository $questionRepository = null,
    IOptionsRepository $optionRepository = null
  ) {
    $this->questRepository = $questRepository ?: new QuestRepository();
    $this->questionRepository = $questionRepository ?: new QuestionsRepository();
    $this->optionRepository = $optionRepository ?: new OptionsRepository();
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

  public function getQuests(IIdentity $identity): array
  {
    $roleString = $identity->getRole()->getName();
    $id = $identity->getId();

    if ($roleString === 'admin') {
      return $this->getAllQuests();
    } else if ($roleString === 'user') {
      return $this->getUserQuests();
    } else if ($roleString === 'creator') {
      return $this->getAllQuests();
    }

    return [];
  }

  private function getAdminQuests(): array
  {
    return $this->questRepository->getQuests();
  }

  private function getUserQuests(): array
  {
    return $this->questRepository->getApprovedQuests();
  }



  // private function getCreatorQuests(): array
  // {
  //   return [
  //     [
  //       'id' => 5,
  //       'name' => 'Creator Quest 1',
  //       'description' => 'This is a creator quest.',
  //       'reward' => 150
  //     ],
  //     [
  //       'id' => 6,
  //       'name' => 'Creator Quest 2',
  //       'description' => 'This is another creator quest.',
  //       'reward' => 250
  //     ]
  //   ];
  // }

  private function getAllQuests(): array
  {
    return array_merge(
      $this->getAdminQuests(),
      $this->getUserQuests(),
      // $this->getCreatorQuests()
    );
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