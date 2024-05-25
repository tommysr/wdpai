<?php

namespace App\Services\Authorize\Quest;

use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\Quest\IAuthorizationStrategyFactory;
use App\Services\Authorize\Quest\QuestRequest;

class AuthorizationFactory implements IAuthorizationStrategyFactory
{
  private $authService;
  private $questProgress;
  private $questRepository;

  public function __construct(IAuthService $authService, IQuestProgressRepository $questProgress, IQuestRepository $questRepository)
  {
    $this->authService = $authService;
    $this->questProgress = $questProgress;
    $this->questRepository = $questRepository;
  }

  public function create(QuestRequest $request): IAuthorizationStrategy
  {
    switch ($request) {
      case QuestRequest::PLAY:
        return new PlayAuthorizationStrategy($this->authService, $this->questProgress, $this->questRepository);
      case QuestRequest::EDIT:
        return new EditAuthorizationStrategy($this->authService, $this->questRepository);
      default:
        throw new \InvalidArgumentException('Invalid request type');
    }
  }
}