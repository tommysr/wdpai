<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IQuestsController extends IRootController
{
  // BASE ACTIONS
  public function getShowQuests(IRequest $request): IResponse;
  public function getShowTopRatedQuests(IRequest $request): IResponse;
  public function getDashboard(IRequest $request): IResponse;
  public function getShowRecommendedQuests(IRequest $request): IResponse;

  // CREATOR ACTIONS
  // create 
  public function getShowCreateQuest(IRequest $request): IResponse;
  public function postCreateQuest(IRequest $request): IResponse;

  // edit
  public function getShowEditQuest(IRequest $request, int $questId): IResponse;
  public function postEditQuest(IRequest $request, int $questId): IResponse;
  public function getShowCreatedQuests(IRequest $request): IResponse;
  public function postUploadQuestPicture(IRequest $request): IResponse;

  // ADMIN ACTIONS
  public function getShowQuestsToApproval(IRequest $request): IResponse;
  public function getShowApprovedQuests(IRequest $request): IResponse;
  public function postPublishQuest(IRequest $request, int $questId): IResponse;
  public function postUnpublishQuest(IRequest $request, int $questId): IResponse;
  public function postAddWallet(IRequest $request, string $blockchain): IResponse;
  public function getRefreshRecommendations(IRequest $request): IResponse;
}