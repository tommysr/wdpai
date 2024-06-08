<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

/**
 * This interface defines the methods that a Quest View Controller class should implement.
 */
interface IQuestViewController extends IRootController
{
  /**
   * Retrieves the quests that need approval and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowQuestsToApproval(IFullRequest $request): IResponse;

  /**
   * Retrieves the approved quests and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowApprovedQuests(IFullRequest $request): IResponse;

  /**
   * Retrieves all quests and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowQuests(IFullRequest $request): IResponse;

  /**
   * Retrieves the top rated quests and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowTopRatedQuests(IFullRequest $request): IResponse;

  /**
   * Retrieves the recommended quests and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowRecommendedQuests(IFullRequest $request): IResponse;

  /**
   * Retrieves the quests created by the user and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @return IResponse The response object.
   */
  public function getShowCreatedQuests(IFullRequest $request): IResponse;

  /**
   * Retrieves the report for a specific quest and returns the response.
   *
   * @param IFullRequest $request The full request object.
   * @param int $questId The ID of the quest.
   * @return IResponse The response object.
   */
  public function getQuestReport(IFullRequest $request, int $questId): IResponse;
}