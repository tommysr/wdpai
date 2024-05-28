<?php

namespace App\Controllers\Interfaces;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IQuestsController extends IRootController
{
  // BASE ACTIONS
  public function getShowQuests(IRequest $request): IResponse;


  // CREATOR ACTIONS
  // create 
  public function getCreateQuest(IRequest $request): IResponse;
  public function postCreateQuest(IRequest $request): IResponse;

  // edit
  public function getEditQuest(IRequest $request, int $questId): IResponse;
  public function postEditQuest(IRequest $request, int $questId): IResponse;
  public function getShowCreatedQuests(IRequest $request): IResponse;


  // ADMIN ACTIONS
  public function getShowQuestsToApproval(IRequest $request): IResponse;
  public function postPublish(IRequest $request, int $questId): IResponse;
  public function postAddWallet(IRequest $request, string $blockchain): IResponse;
  public function postEnterQuest(IRequest $request, int $walletId): IResponse;
}