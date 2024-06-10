<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\IAuthResult;
use App\Services\Authorize\Quest\QuestRequest;

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
  public function authorizeQuest(string $request, int $questId = null): IAuthResult;
}
