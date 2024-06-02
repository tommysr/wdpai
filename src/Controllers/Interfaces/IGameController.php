<?php

namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IRequest;

interface IGameController extends IRootController
{
  public function postEnterQuest(IRequest $request, int $walletId): IResponse;
  public function getPlay(IRequest $request): IResponse;
  // public function getQuestion(IRequest $request, int $questId, int $questionId): IResponse;
  public function postAnswer(IRequest $request, int $questionId): IResponse;
  public function postRating(IRequest $request): IResponse;
  // public function startGame(int $userId, int $gameId): void;
  // public function finishGame(int $userId, int $gameId): void;
  // public function abandonGame(int $userId, int $gameId): void;
  // public function getGameProgress(int $userId, int $gameId): void;
}