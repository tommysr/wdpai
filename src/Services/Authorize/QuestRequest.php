<?php

namespace App\Services\Authorize;

enum QuestRequest
{
  case PLAY;
  case ENTER;
  case CREATE;
  case EDIT;
  case PUBLISH;
}


