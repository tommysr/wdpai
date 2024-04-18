<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Option.php';


class OptionsRepository extends Repository
{
  public function getOptionsByQuestionId(int $questionId): array
  {
    $options = [];

    $sql = "SELECT *
    FROM Options o
    WHERE o.QuestionID = :questionId;";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questionId' => $questionId]);
    $optionsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($optionsFetched as $option) {
      $options[] = new Option($option['optionid'], $option['questionid'], $option['text'], $option['isCorrect']);
    }


    return $options;
  }
}
