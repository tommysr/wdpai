<?php

namespace App\Controllers\Interfaces;

use App\Request\Request;
use App\Middleware\IResponse;
use App\Controllers\Interfaces\IRootController;

interface IQuestsController extends IRootController
{
  public function getShowQuests(Request $request): IResponse;
  // public function showQuestWallets(Request $request): string;
  // public function startQuest(Request $request): string;
  // public function createQuest(Request $request): string;
  // public function editQuest(Request $request): string;
  // public function createdQuests(Request $request): string;
}