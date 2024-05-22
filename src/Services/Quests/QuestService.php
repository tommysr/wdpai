<?php

namespace App\Services\Quests;

use App\Repository\IOptionsRepository;
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

  public function getQuests(IIdentity $identity): array
  {
    $role = $identity->getRole();
    $id = $identity->getId();

    if ($role === 'admin') {
      return $this->getAdminQuests();
    } else if ($role === 'user') {
      return $this->getUserQuests();
    } else {
      return $this->getAllQuests();
    }
  }

  private function getAdminQuests(): array
  {
    return [
      [
        'id' => 1,
        'name' => 'Admin Quest 1',
        'description' => 'This is an admin quest.',
        'reward' => 100
      ],
      [
        'id' => 2,
        'name' => 'Admin Quest 2',
        'description' => 'This is another admin quest.',
        'reward' => 200
      ]
    ];
  }

  private function getUserQuests(): array
  {
    return [
      [
        'id' => 3,
        'name' => 'User Quest 1',
        'description' => 'This is a user quest.',
        'reward' => 50
      ],
      [
        'id' => 4,
        'name' => 'User Quest 2',
        'description' => 'This is another user quest.',
        'reward' => 75
      ]
    ];
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

  public function getCreatorQuests(IIdentity $identity): array
  {
    $quests = $this->questRepository->getCreatorQuests($identity->getId());

    array_filter($quests, function ($quest) {
      return !$quest->isApproved();
    });

    return $quests;
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