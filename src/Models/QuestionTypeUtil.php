<?php

namespace App\Models;

use App\Models\QuestionType;


class QuestionTypeUtil
{
  public static function toString(QuestionType $type): string
  {
    return match ($type) {
      QuestionType::SINGLE_CHOICE => 'single_choice',
      QuestionType::MULTIPLE_CHOICE => 'multiple_choice',
      QuestionType::READ_TEXT => 'read_text',
      default => 'unknown',
    };
  }
}


function getQuestionTypeFromName(string $name): QuestionType
{
  $lookup = [
    'single_choice' => QuestionType::SINGLE_CHOICE,
    'multiple_choice' => QuestionType::MULTIPLE_CHOICE,
    'read_text' => QuestionType::READ_TEXT,
  ];

  return $lookup[$name] ?? QuestionType::UNKNOWN;
}
