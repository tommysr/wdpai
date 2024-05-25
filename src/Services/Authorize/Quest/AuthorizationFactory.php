<?php

namespace App\Services\Authorize\Quest;

use App\Repository\IQuestRepository;
use App\Repository\QuestProgress\IQuestProgressRepository;
use App\Services\Authenticate\IAuthService;
use App\Services\Authorize\Quest\IAuthorizationStrategyFactory;
use App\Services\Authorize\Quest\QuestRequest;
use App\Services\Session\ISessionService;

class AuthorizationFactory implements IAuthorizationStrategyFactory
{
  private $session;
  private $authService;
  private $questProgress;
  private $questRepository;

  public function __construct(ISessionService $session, IAuthService $authService, IQuestProgressRepository $questProgress, IQuestRepository $questRepository)
  {
    $this->session = $session;
    $this->authService = $authService;
    $this->questProgress = $questProgress;
    $this->questRepository = $questRepository;
  }

  public function create(QuestRequest $request): IAuthorizationStrategy
  {
    switch ($request) {
      case QuestRequest::ACCESS:
        return new PlayAuthorizationStrategy($this->authService, $this->session, $this->questProgress, $this->questRepository);
      case QuestRequest::EDIT:
        return new EditAuthorizationStrategy($this->authService, $this->questRepository);
      default:
        throw new \InvalidArgumentException('Invalid request type');
    }
  }
}