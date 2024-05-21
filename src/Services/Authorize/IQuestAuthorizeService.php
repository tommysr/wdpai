<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAuthResult;

/**
 * Interface for authorizing quests.
 */
interface IQuestAuthorizeService
{
  /**
   * Authorize a quest request.
   * 
   * @param QuestRequest $request The quest request type.
   * @param int|null $questId The ID of the quest (if applicable).
   * @throws \Exception If authorization fails.
   */
  public function authorizeQuest(QuestRequest $request, int $questId = null): IAuthResult;
}
