<?php
namespace App\Controllers\Interfaces;

use App\Middleware\IResponse;
use App\Request\IFullRequest;

interface IAdminController extends IRootController
{
  public function postPublishQuest(IFullRequest $request, int $questId): IResponse;
  public function postUnpublishQuest(IFullRequest $request, int $questId): IResponse;
  public function getRefreshRecommendations(IFullRequest $request): IResponse;
  public function getPromoteUser(IFullRequest $request, string $userName): IResponse;
}