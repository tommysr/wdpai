<?php

namespace App\Middleware\QuestValidation;

use App\Validator\IValid;


class QuestionsRule implements IValid
{
  public function validate($data): bool|string
  {
    if (!isset($data) || !is_array($data) || empty($data)) {
      return "Questions field is required";
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

      if (strlen($question['text']) < 3 || strlen($question['text']) > 500) {
        return "Question must be between 3 and 500 characters";
      }

      $isCorrectCount = 0;
      $hasOptions = false;

      if (isset($question['options']) && is_array($question['options'])) {
        $hasOptions = true;

        foreach ($question['options'] as $option) {
          if (!isset($option['text'])) {
            return "Each option must have 'text' and 'isCorrect' fields.";
          }

          if(strlen($option['text']) == 0 || strlen($option['text']) > 50) {
            return "Option must be between 1 and 50 characters";
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