<?php

namespace App\Models;

enum QuestionType: string
{
  case SINGLE_CHOICE = 'single_choice';
  case MULTIPLE_CHOICE = 'multiple_choice';
  case READ_TEXT = 'read_text';
  case UNKNOWN = 'unknown';
}
