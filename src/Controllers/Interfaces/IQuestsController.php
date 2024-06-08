<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IQuestsController extends IRootController
{
  // BASE ACTIONS
  public function getShowQuests(IFullRequest $request): IResponse;
  public function getShowTopRatedQuests(IFullRequest $request): IResponse;
  public function getShowRecommendedQuests(IFullRequest $request): IResponse;

  // CREATOR ACTIONS
  // create 
  public function getShowCreateQuest(IFullRequest $request): IResponse;
  public function postCreateQuest(IFullRequest $request): IResponse;
  public function getReportQuest(IFullRequest $request, int $questId): IResponse;

  // edit
  public function getShowEditQuest(IFullRequest $request, int $questId): IResponse;
  public function postEditQuest(IFullRequest $request, int $questId): IResponse;
  public function getShowCreatedQuests(IFullRequest $request): IResponse;
  public function postUploadQuestPicture(IFullRequest $request): IResponse;

  // ADMIN ACTIONS
  public function getShowQuestsToApproval(IFullRequest $request): IResponse;
  public function getShowApprovedQuests(IFullRequest $request): IResponse;
  public function postPublishQuest(IFullRequest $request, int $questId): IResponse;
  public function postUnpublishQuest(IFullRequest $request, int $questId): IResponse;
  public function postAddWallet(IFullRequest $request, string $blockchain): IResponse;
  public function getRefreshRecommendations(IFullRequest $request): IResponse;
}