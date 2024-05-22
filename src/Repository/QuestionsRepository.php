<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Models\IQuestion;
use App\Models\Question;
use App\Models\QuestionTypeUtil;

interface IQuestionsRepository
{
  public function getById(int $questionId): ?IQuestion;
  public function getQuestionsByQuestId(int $questId);
  public function deleteQuestions(array $questions): void;
  public function deleteAllQuestions(int $questId): void;
  public function updateQuestions(array $questions): void;
  public function saveQuestion(IQuestion $question): int;
  public function saveQuestions(array $questions): void;
}

class QuestionsRepository extends Repository implements IQuestionsRepository
{
  private function constructQuestionModel(array $question): IQuestion
  {
    $type = QuestionTypeUtil::getQuestionTypeFromName($question['type']);
    return new Question($question['questionid'], $question['questid'], $question['text'], $type);
  }

  public function deleteAllQuestions(int $questId): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM questions WHERE questid = :questid';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':questid' => $questId,
      ]);

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }


  public function getById(int $questionId): ?IQuestion
  {
    $sql = "SELECT * FROM Questions where QuestionID = :questionId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(":questionId", $questionId, \PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $this->constructQuestionModel($result);
  }


  public function getQuestionsByQuestId($questId): array
  {

    $sql = "SELECT * FROM Questions WHERE QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['questId' => $questId]);
    $questionsFetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $questions = [];

    foreach ($questionsFetched as $question) {
      $questions[] = $this->constructQuestionModel($question);
    }

    return $questions;
  }

  public function deleteQuestions(array $questions): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM questions WHERE questionid = :questionid';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':questionid' => $question->getQuestionId(),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function deleteAllQuestionsForQuest(int $questId): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM questions WHERE questid = :questid';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':questid' => $questId,
      ]);

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function updateQuestions(array $questions): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'UPDATE questions SET text = :text, type = :type WHERE questionid = :questionid';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':questionid' => $question->getQuestionId(),
          ':text' => $question->getText(),
          ':type' => QuestionTypeUtil::toString($question->getType()),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveQuestion(IQuestion $question): int
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'INSERT INTO questions (questid, text, type) VALUES (:questid, :text, :type)';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':questid' => $question->getQuestId(),
        ':text' => $question->getText(),
        ':type' => QuestionTypeUtil::toString($question->getType()),
      ]);

      $pdo->commit();

      return $pdo->lastInsertId();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveQuestions(array $questions): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'INSERT INTO questions (questid, text, type) VALUES (:questid, :text, :type)';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':questid' => $question->getQuestId(),
          ':text' => $question->getText(),
          ':type' => QuestionTypeUtil::toString($question->getType()),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }
}
