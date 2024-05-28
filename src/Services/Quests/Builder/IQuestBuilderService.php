<?php

namespace App\Services\Quests\Builder;

use App\Models\IQuest;
use App\Services\Quests\Builder\IQuestBuilder;

interface IQuestBuilderService
{
  public function setBuilder(IQuestBuilder $builder): void;
  public function buildQuest(array $questData): IQuest;
}