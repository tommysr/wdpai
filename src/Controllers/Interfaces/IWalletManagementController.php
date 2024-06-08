<?php

namespace App\Controllers\Interfaces;

use App\Request\IFullRequest;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IWalletManagementController extends IRootController
{
  public function postAddWallet(IFullRequest $request, string $blockchain): IResponse;
  public function getShowQuestWallets(IFullRequest $request, int $questId): IResponse;
}