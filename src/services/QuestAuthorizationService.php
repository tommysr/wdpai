<?php

require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../exceptions/Quests.php';



enum QuestAuthorizeRequest
{
  case ENTER;
  case CREATE;
  case EDIT;
}


class QuestAuthorizationService
{
  private QuestStatisticsRepository $questStatisticsRepository;
  private SessionService $sessionService;
  private QuestRepository $questRepository;

  public function __construct(QuestStatisticsRepository $questStatisticsRepository = null, SessionService $sessionService = null, QuestRepository $questRepository = null)
  {
    $this->questStatisticsRepository = $questStatisticsRepository ?: new QuestStatisticsRepository();
    $this->sessionService = $sessionService ?: new SessionService();
    $this->questRepository = $questRepository ?: new QuestRepository();
  }

  public function authorizeQuestAction(QuestAuthorizeRequest $request, ?int $questId = null)
  {
    $userId = $this->getSignedUserId();

    if ($userId === null) {
      throw new NotLoggedInException('you need to log in');
    }

    switch ($request) {
      case QuestAuthorizeRequest::ENTER:
        return $this->checkParticipationRequest($questId);
      case QuestAuthorizeRequest::CREATE:
        return $this->checkCreateRequest();
      case QuestAuthorizeRequest::EDIT:
        return $this->checkEditRequest($questId);
    }
  }

  private function getSignedUserId(): ?int
  {
    $userSession = $this->sessionService->get('user');
    return $userSession ? $userSession['id'] : null;
  }

  private function getSignedUserRole(): ?string
  {
    $userSession = $this->sessionService->get('user');
    return $userSession ? $userSession['role'] : null;
  }

  private function canUserCreate(): bool
  {
    return $this->getSignedUserRole() === 'admin';
  }

  private function checkEditRequest(int $questId)
  {
    $id = $this->getSignedUserId();
    $quest = $this->questRepository->getQuestById($questId);

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    }

    if ($quest === null) {
      throw new NotFoundException('quest not found');
    }

    if ($quest->getCreatorId() !== $id) {
      throw new AuthorizationException('Quest is not yours.');
    }
  }

  private function checkCreateRequest()
  {
    if ($this->getSignedUserRole() !== 'admin') {
      throw new AuthorizationException('user is not an admin');
    }
  }

  private function checkParticipationRequest(int $questId)
  {
    $id = $this->getSignedUserId();

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    } else if ($this->questStatisticsExists($id, $questId)) {
      throw new AuthorizationException('You can not reenter quest.');
    }
  }

  public function questStatisticsExists($userId, $questId): bool
  {
    if ($this->questStatisticsRepository->getQuestStatistic($userId, $questId)) {
      return true;
    }

    return false;
  }
}
