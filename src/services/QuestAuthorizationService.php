<?php

require_once __DIR__ . '/../repository/QuestRepository.php';
require_once __DIR__ . '/../services/SessionService.php';
require_once __DIR__ . '/../exceptions/Quests.php';



enum QuestRole
{
  case GUEST;
  case NORMAL;
  case CREATOR;
  case ADMIN;
}

function getQuestRoleFromString(string $role): QuestRole
{
  switch ($role) {
    case 'normal':
      return QuestRole::NORMAL;
    case 'creator':
      return QuestRole::CREATOR;
    case 'admin':
      return QuestRole::ADMIN;
  }

  return QuestRole::GUEST;
}

enum QuestAuthorizeRequest
{
  case ENTER;
  case CREATE;
  case EDIT;
  case PUBLISH;
}


class QuestAuthorizationService
{
  private QuestStatisticsRepository $questStatisticsRepository;
  private QuestRepository $questRepository;

  private ?int $userId = null;
  private QuestRole $role = QuestRole::GUEST;


  public function __construct(QuestStatisticsRepository $questStatisticsRepository = null, QuestRepository $questRepository = null)
  {
    $this->questStatisticsRepository = $questStatisticsRepository ?: new QuestStatisticsRepository();

    $user = SessionService::get('user');

    if ($user) {
      $this->userId = $user['id'];
      $this->role = getQuestRoleFromString($user['role']);
    } else {
      $this->userId = null;
      $this->role = QuestRole::GUEST;
    }


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
      case QuestAuthorizeRequest::PUBLISH:
        return $this->checkPublishRequest();
    }
  }

  public function getSignedUserId(): ?int
  {
    return $this->userId;
  }

  public function getSignedUserRole(): QuestRole
  {
    return $this->role;
  }

  public function checkPublishRequest()
  {
    if ($this->role !== QuestRole::ADMIN) {
      throw new AuthorizationException('You are not admin');
    }
  }

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

  public function checkCreateRequest()
  {
    if ($this->role !== QuestRole::CREATOR && $this->role !== QuestRole::ADMIN) {
      throw new AuthorizationException('user is not an admin');
    }
  }

  public function checkParticipationRequest(int $questId)
  {
    $id = $this->userId;

    if ($id === null) {
      throw new NotLoggedInException('you need to log in');
    } else if ($this->questStatisticsRepository->getQuestStatistic($id, $questId)) {
      throw new AuthorizationException('You can not reenter quest.');
    }
  }
}
