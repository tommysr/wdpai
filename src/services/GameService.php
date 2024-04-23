<?php

require_once __DIR__ . '/../repository/QuestionsRepository.php';

class GameService
{
  private QuestionsRepository $questionsRepository;
  private OptionsRepository $optionsRepository;

  public function __construct()
  {
    $this->questionsRepository = new QuestionsRepository();
    $this->optionsRepository = new OptionsRepository();
  }

  private function scaleValueToDiscreet($value, $minInput, $maxInput, $minOutput, $maxOutput)
  {
    $value = max(min($value, $maxInput), $minInput);
    $ratio = ($value - $minInput) / ($maxInput - $minInput);
    $scaledValue = $minOutput + ($maxOutput - $minOutput) * $ratio;
    $discreteValue = round($scaledValue);
    return $discreteValue;
  }

  public function processUserResponse(int $questionId, array $selectedOptionsIds)
  {
    $correctOptionsIds = $this->optionsRepository->getCorrectOptionsIdsForQuestionId($questionId);
    $score = $this->calculateScore($selectedOptionsIds, $correctOptionsIds);
    $maxScore = count($correctOptionsIds);
    $correctPercentage = $score / $maxScore;

    return [
      'score' => $score,
      'maxScore' => $maxScore,
      'correctPercentage' => $correctPercentage,
      'stars' => $this->scaleValueToDiscreet($correctPercentage, 1, 100, 0, 3)
    ];
  }

  private function calculateScore(array $selectedOptionsIds, array $correctOptionsIds): int
  {
    $score = 0;

    foreach ($selectedOptionsIds as $selectedOption) {
      if (in_array($selectedOption, $correctOptionsIds)) {
        $score++;
      } else {
        $score--;
      }
    }

    return max($score, 0);
  }
}
