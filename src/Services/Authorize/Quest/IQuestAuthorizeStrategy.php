<?php

namespace App\Services\Authorize\Quest;

use App\Services\Authorize\IAuthResult;

interface IQuestAuthorizeStrategy
{
    public function authorize(int $questId = null): IAuthResult;
}