<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Question.php';

class QuestionsRepository extends Repository
{
  public function getQuestionsByQuestId($questId)
  {

    $sql = "SELECT * FROM Questions WHERE QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questId' => $questId]);
    $questionsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $questions = [];

    foreach ($questionsFetched as $question) {
      $type = QuestionType::fromName($question['type']);
      $questions[] = new Question($question['questionid'], $question['questid'], $question['text'], $type);
    }

    return $questions;
  }
}
