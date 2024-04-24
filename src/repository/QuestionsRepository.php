<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Question.php';

class QuestionsRepository extends Repository
{
  private function constructQuestionModel(array $question): Question
  {
    $type = QuestionType::fromName($question['type']);
    return new Question($question['questionid'], $question['questid'], $question['text'], $type);
  }


  public function getById($questionId): ?Question
  {
    $sql = "SELECT * FROM Questions where QuestionID = :questionId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(":questionId", $questionId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $this->constructQuestionModel($result);
  }


  public function getQuestionsByQuestId($questId)
  {

    $sql = "SELECT * FROM Questions WHERE QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questId' => $questId]);
    $questionsFetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $questions = [];

    foreach ($questionsFetched as $question) {
      $questions[] = $this->constructQuestionModel($question);
    }

    return $questions;
  }
}