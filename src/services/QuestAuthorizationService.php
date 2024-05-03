<?php

class QuestAuthorizationService
{
  private QuestStatisticsRepository $questStatisticsRepository;

  public function __construct(QuestStatisticsRepository $questStatisticsRepository = null)
  {
    $this->questStatisticsRepository = $questStatisticsRepository ?: new QuestStatisticsRepository();
  }

  public function questStatisticsExists($userId, $questId): bool
  {
    if ($this->questStatisticsRepository->getQuestStatistic($userId, $questId)) {
      return true;
    }

    return false;
  }
}
