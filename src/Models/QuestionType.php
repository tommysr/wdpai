<?php

namespace App\Models;

enum QuestionType
{
  case SINGLE_CHOICE;
  case MULTIPLE_CHOICE;
  case READ_TEXT;
  case UNKNOWN;
}
