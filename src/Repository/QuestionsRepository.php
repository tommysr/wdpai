<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Models\IQuestion;
use App\Models\Question;


class QuestionsRepository extends Repository implements IQuestionsRepository
{
  private function constructQuestionModel(array $question): IQuestion
  {
    return new Question($question['question_id'], $question['quest_id'], $question['text'], $question['type'], $question['points']);
  }

  public function deleteQuestionById(int $id): void
  {
    $pdo = $this->db->connect();

    $sql = 'DELETE FROM questions WHERE question_id = :question_id';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      ':question_id' => $id,
    ]);
  }
  public function deleteQuestion(IQuestion $question): void
  {
    $pdo = $this->db->connect();

    $sql = 'DELETE FROM questions WHERE question_id = :question_id';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      ':question_id' => $question->getQuestionId(),
    ]);
  }

  public function deleteAllQuestions(int $questId): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM questions WHERE quest_id = :quest_id';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':quest_id' => $questId,
      ]);

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }


  public function getById(int $questionId): ?IQuestion
  {
    $sql = "SELECT * FROM questions where question_id = :question_id";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(":question_id", $questionId, \PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $this->constructQuestionModel($result);
  }


  public function getQuestionsByQuestId($questId): array
  {

    $sql = "SELECT * FROM questions WHERE quest_id = :questId";

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
      $sql = 'DELETE FROM questions WHERE question_id = :question_id';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':question_id' => $question->getQuestionId(),
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
      $sql = 'DELETE FROM questions WHERE quest_id = :quest_id';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':quest_id' => $questId,
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
      $sql = 'UPDATE questions SET text = :text, type = :type, points = :points WHERE question_id = :question_id';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':question_id' => $question->getQuestionId(),
          ':text' => $question->getText(),
          ':type' => $question->getType(),
          ':points' => $question->getPoints(),
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
      $sql = 'INSERT INTO questions (quest_id, text, type, points) VALUES (:quest_id, :text, :type, :points)';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':quest_id' => $question->getQuestId(),
        ':text' => $question->getText(),
        ':type' => $question->getType(),
        ':points' => $question->getPoints(),
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
      $sql = 'INSERT INTO questions (quest_id, text, type, points) VALUES (:quest_id, :text, :type, :points)';
      $stmt = $pdo->prepare($sql);

      foreach ($questions as $question) {
        $stmt->execute([
          ':quest_id' => $question->getQuestId(),
          ':text' => $question->getText(),
          ':type' => $question->getType(),
          ':points' => $question->getPoints(),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function getFirstQuestionId(int $questId): ?int
  {
    $sql = "SELECT question_id FROM questions WHERE quest_id = :quest_id ORDER BY question_id ASC LIMIT 1";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['quest_id' => $questId]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $result['question_id'];
  }

  public function getNextQuestionId(int $questId, int $currentQuestionId): ?int
  {
    $sql = "SELECT question_id FROM questions WHERE quest_id = :quest_id AND question_id > :current_question_id ORDER BY question_id ASC LIMIT 1";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['quest_id' => $questId, 'current_question_id' => $currentQuestionId]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $result['question_id'];
  }

  public function getLastQuestionId(int $questId): ?int
  {
    $sql = "SELECT question_id FROM questions WHERE quest_id = :quest_id ORDER BY question_id DESC LIMIT 1";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['quest_id' => $questId]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (empty($result)) {
      return null;
    }

    return $result['question_id'];
  }

  public function getNextQuestion(int $questId, int $currentQuestionId): ?IQuestion
  {
    $nextQuestionId = $this->getNextQuestionId($questId, $currentQuestionId);

    if (!$nextQuestionId) {
      return null;
    }

    return $this->getById($nextQuestionId);
  }
}
