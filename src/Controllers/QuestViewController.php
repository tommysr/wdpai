<?php

namespace App\Controllers;

use App\Controllers\AppController;
use App\Controllers\Interfaces\IQuestViewController;
use App\Middleware\JsonResponse;
use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Services\Authenticate\IAuthService;
use App\Services\QuestProgress\IQuestProgressProvider;
use App\Services\QuestProgress\IQuestProgressRetrievalService;
use App\Services\Quests\IQuestProvider;
use App\Services\Quests\IQuestService;
use App\Services\Recommendation\IRecommendationService;
use App\Services\Session\ISessionService;
use App\View\IViewRenderer;

class QuestViewController extends AppController implements IQuestViewController
{
  private IQuestProvider $questProvider;
  private IAuthService $authService;
  private IRecommendationService $recommendationService;
  private IQuestProgressProvider $questProgressProvider;

  public function __construct(
    IFullRequest $request,
    ISessionService $sessionService,
    IViewRenderer $viewRenderer,
    IQuestProvider $questProvider,
    IAuthService $authService,
    IQuestProgressProvider $questProgressProvider,
    IRecommendationService $recommendationService
  ) {
    parent::__construct($request, $sessionService, $viewRenderer);
    $this->questProvider = $questProvider;
    $this->authService = $authService;
    $this->questProgressProvider = $questProgressProvider;
    $this->recommendationService = $recommendationService;
  }

  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->getShowQuests($request);
  }

  public function getShowQuestsToApproval(IFullRequest $request): IResponse
  {
    $quests = $this->questProvider->getQuestsToApproval();

    return $this->render('layout', ['title' => 'quests to approval', 'quests' => $quests], 'adminQuests');
  }

  public function getShowApprovedQuests(IFullRequest $request): IResponse
  {
    $quests = $this->questProvider->getApprovedQuests();

    return $this->render('layout', ['title' => 'approved quests', 'quests' => $quests], 'adminQuests');
  }

  public function getQuestReport(IFullRequest $request, int $questId): IResponse
  {
    $quest = $this->questProvider->getQuestWithQuestions($questId);

    if ($quest->getCreatorId() !== $this->authService->getIdentity()->getId()) {
      return new JsonResponse(['errors' => ['its not your quest']]);
    }

    $questionsArray = [];

    foreach ($quest->getQuestions() as $question) {
      $optionsArray = [];

      foreach ($question->getOptions() as $option) {
        $responseCount = $this->questProgressProvider->getResponsesCount($option->getOptionId());
        $optionsArray[] = [
          'option_id' => $option->getOptionId(),
          'text' => $option->getText(),
          'is_correct' => $option->getIsCorrect(),
          'response_count' => $responseCount
        ];
      }

      $questionsArray[] = [
        'question_id' => $question->getQuestionId(),
        'text' => $question->getText(),
        'type' => $question->getType(),
        'points' => $question->getPoints(),
        'options' => $optionsArray
      ];
    }

    $questReport = [
      'quest_id' => $quest->getQuestID(),
      'title' => $quest->getTitle(),
      'description' => $quest->getDescription(),
      'expiry_date' => $quest->getExpiryDateString(),
      'participants_count' => $quest->getParticipantsCount(),
      'participants_limit' => $quest->getParticipantsLimit(),
      'avg_rating' => $quest->getAvgRating(),
      'blockchain' => $quest->getBlockchain(),
      'payout_date' => $quest->getPayoutDate(),
      'required_minutes' => $quest->getRequiredMinutes(),
      'pool_amount' => $quest->getPoolAmount(),
      'token' => $quest->getToken(),
      'creator_id' => $quest->getCreatorId(),
      'questions' => $questionsArray
    ];

    return new JsonResponse($questReport);
  }


  public function getShowQuests(IFullRequest $request): IResponse
  {
    $id = $this->authService->getIdentity()->getId();
    $quests = $this->questProvider->getQuestsToPlay();

    $quests = array_filter($quests, function ($quest) use ($id) {
      return !$this->questProgressProvider->isQuestPlayed($id, $quest->getQuestID());
    });

    return $this->render('layout', ['title' => 'quest list', 'quests' => $quests], 'quests');
  }

  public function getShowTopRatedQuests(IFullRequest $request): IResponse
  {
    $quests = $this->questProvider->getTopRatedQuests();

    return new JsonResponse(['quests' => $quests], 200);
  }

  public function getShowRecommendedQuests(IFullRequest $request): IResponse
  {
    $userId = $this->authService->getIdentity()->getId();
    $questsIds = $this->recommendationService->getRecommendations($userId);
    $quests = $this->questProvider->getQuestsByIds($questsIds);

    return new JsonResponse(['quests' => $quests], 200);
  }

  // show created quests list which are not approved yet, but can be edited by creator
  public function getShowCreatedQuests(IFullRequest $request): IResponse
  {
    $quests = $this->questProvider->getCreatorQuests($this->authService->getIdentity());

    return $this->render('layout', ['title' => 'created quests', 'quests' => $quests], 'createdQuests');
  }
}