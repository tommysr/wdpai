<?php

namespace App\Middleware\QuestValidation;

use App\Validator\IValid;


class QuestionsRule implements IValid
{
  public function validate($data): bool|string
  {
    if (!isset($data) || !is_array($data)) {
      return "Questions field is required and must be an array.";
    }

    foreach ($data as $question) {
      if (
        !isset($question['text'])
      ) {
        return "Each question must have 'text'";
      }

      if ($question['text'] === '') {
        return "Each question must have 'text'";
      }

      $isCorrectCount = 0;
      $hasOptions = false;

      if (isset($question['options']) && is_array($question['options'])) {
        $hasOptions = true;

        foreach ($question['options'] as $option) {
          if (!isset($option['text'])) {
            return "Each option must have 'text' and 'isCorrect' fields.";
          }

          if (isset($option['isCorrect'])) {
            $isCorrectCount++;
          }
        }
      }

      if ($isCorrectCount === 0 && $hasOptions) {
        return "Each question with options must have at least one correct option.";
      }
    }

    return true;
  }
}