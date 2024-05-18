<?php

require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../exceptions/Quests.php';


/**
 * Interface for authorizing quests.
 */
interface QuestAuthorizer
{
  /**
   * Authorize a quest request.
   * 
   * @param QuestRequest $request The quest request type.
   * @param int|null $questId The ID of the quest (if applicable).
   * @throws AuthorizationException If authorization fails.
   */
  public function authorizeQuest(QuestRequest $request, int $questId = null): void;
}

/**
 * Enum representing different types of quest requests.
 */
enum QuestRequest
{
  case PLAY;
  case ENTER;
  case CREATE;
  case EDIT;
  case PUBLISH;
}


class QuestAuthorizationMiddleware implements Middleware
{
  private QuestAuthorizer $questAuthorizer;
  private QuestRequest $questRequest;
  private int $questId;
  private ?Middleware $next = null;

  public function __construct(QuestAuthorizer $questAuthorizer, QuestRequest $questRequest, int $questId)
  {
    $this->questAuthorizer = $questAuthorizer;
    $this->questRequest = $questRequest;
    $this->questId = $questId;
  }

  public function setNext(Middleware $middleware): Middleware
  {
    $this->next = $middleware;
    return $middleware;
  }

  public function handle(): void
  {
    try {
      $this->questAuthorizer->authorizeQuest($this->questRequest, $this->questId);
    } catch (AuthorizationException $e) {
      Redirector::redirectTo('/login');
    }

    if ($this->next !== null) {
      $this->next->handle();
    }
  }
}

class QuestAuthorizeService extends RoleAuthorizationService implements QuestAuthorizer
{
  private IQuestStatisticsRepository $questStatisticsRepository;
  private IQuestRepository $questRepository;

  public function __construct(IQuestStatisticsRepository $questStatisticsRepository = null, IQuestRepository $questRepository = null, ISessionService $sessionService = null)
  {
    parent::__construct($sessionService);
    $this->questStatisticsRepository = $questStatisticsRepository ?: new QuestStatisticsRepository();
    $this->questRepository = $questRepository ?: new QuestRepository();
  }


  /**
   * Authorize a quest request.
   * 
   * @param QuestRequest $request The quest request type.
   * @param int|null $questId The ID of the quest (if applicable).
   * @throws AuthorizationException If authorization fails.
   */
  public function authorizeQuest(QuestRequest $request, int $questId = null): void
  {
    switch ($request) {
      case QuestRequest::PLAY:
        $this->checkGameplayRequest($questId);
        break;
      case QuestRequest::ENTER:
        $this->checkParticipationRequest($questId);
        break;
      case QuestRequest::CREATE:
        $this->checkCreateRequest();
        break;
      case QuestRequest::EDIT:
        $this->checkEditRequest($questId);
        break;
      case QuestRequest::PUBLISH:
        $this->checkPublishRequest();
        break;
      default:
        throw new InvalidArgumentException('Invalid quest request.');
    }
  }

  /**
   * Check authorization for publishing a quest.
   * 
   * @throws AuthorizationException If authorization fails.
   */
  public function checkPublishRequest()
  {
    if ($this->role !== Role::ADMIN) {
      throw new AuthorizationException('You are not admin');
    }
  }

  /**
   * Check authorization for editing a quest.
   * 
   * @param int $questId The ID of the quest.
   * 
   * @throws NotFoundException If the quest is not found.
   * @throws AlreadyApproved If the quest is already approved.
   * @throws NotLoggedInException If the user is not logged in.
   * @throws AuthorizationException If the quest is not owned by the user.
   * 
   */
  public function checkEditRequest(int $questId)
  {
    $id = $this->userId;
    $quest = $this->questRepository->getQuestById($questId);

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    }

    if ($quest === null) {
      throw new NotFoundException('quest not found');
    }

    if ($quest->isApproved()) {
      throw new AlreadyApproved('quest cannot be edited');
    }

    if ($quest->getCreatorId() !== $id) {
      throw new AuthorizationException('Quest is not yours.');
    }
  }

  /**
   * Check authorization for creating a quest.
   * 
   * @throws AuthorizationException If authorization fails.
   */
  public function checkCreateRequest()
  {
    if ($this->role !== Role::CREATOR && $this->role !== Role::ADMIN) {
      throw new AuthorizationException('user is not an admin');
    }
  }

  /**
   * Check authorization for gameplay request.
   * 
   * @param int $questId The ID of the quest.
   * 
   * @throws NotLoggedInException If the user is not logged in.
   * @throws GameplayInProgressException If the user has a gameplay in progress.
   */
  public function checkGameplayRequest(int $questId)
  {
    $id = $this->userId;

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    }

    $gameplayToResume = $this->questStatisticsRepository->getQuestIdToFinish($id);

    if ($gameplayToResume === null) {
      return;
    }

    if ($gameplayToResume !== $questId) {
      throw new GameplayInProgressException($gameplayToResume);
    }
  }

  /**
   * Check authorization for participation request.
   * 
   * @param int $questId The ID of the quest.
   * 
   * @throws NotLoggedInException If the user is not logged in.
   * @throws GameplayInProgressException If the user has a gameplay in progress.
   * @throws AuthorizationException If the user has already participated in the quest.
   */
  public function checkParticipationRequest(int $questId)
  {
    $id = $this->userId;

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    }

    $gameplayToResume = $this->questStatisticsRepository->getQuestIdToFinish($id);

    if ($gameplayToResume) {
      throw new GameplayInProgressException($gameplayToResume);
    }

    // $questStats = $this->questStatisticsRepository->getQuestStatistic($id, $questId);

    // // check if user already participated in quest, maybe need to somehow redirect to current gameplay 
    // if ($questStats) {
    //   if ($questStats->getCompletionDate()) {
    //     throw new AuthorizationException('You can not reenter quest.');
    //   } else {
    //     throw new GameplayInProgressException('You are already in game');
    //   }
    // }
  }
}