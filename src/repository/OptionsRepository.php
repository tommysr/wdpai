<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Option.php';


class OptionsRepository extends Repository
{

  public function getCorrectOptionsIdsForQuestionId(int $questionId): array {
    $options = [];

    $sql = "SELECT OptionID FROM Options WHERE QuestionID = :questionId AND isCorrect = true";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questionId' => $questionId]);
    $optionsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($optionsFetched as $option) {
      $options[] = $option['optionid'];
    }

    return $options;
  }

  public function getOptionsByQuestionId(int $questionId): array
  {
    $options = [];

    $sql = "SELECT * FROM Options WHERE QuestionID = :questionId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questionId' => $questionId]);
    $optionsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($optionsFetched as $option) {
      $options[] = new Option($option['optionid'], $option['questionid'], $option['text'], $option['iscorrect']);
    }


    return $options;
  }
}
