<?php

class QuestAuthorizationService
{
  private $questStatisticsRepository;

  public function __construct()
  {
    $this->questStatisticsRepository = new QuestStatisticsRepository();
  }

  public function isUserAuthorized($userId, $questId)
  {

    if ($this->questStatisticsExists($userId, $questId)) {
      return false;
    }

    return true;
  }

  private function questStatisticsExists($userId, $questId)
  {
    if ($this->questStatisticsRepository->getQuestStatistic($userId, $questId)) {
      return true;
    }

    return false;
  }
}
