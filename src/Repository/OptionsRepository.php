<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Option.php';


class OptionsRepository extends Repository
{

  public function getCorrectOptionsIdsForQuestionId(int $questionId): array
  {
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



  public function updateOptions(array $options)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'UPDATE options SET questionid = :questionid, text = :text, iscorrect = :iscorrect WHERE optionid = :optionid';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':questionid' => $option->getQuestionId(),
          ':text' => $option->getText(),
          ':iscorrect' => $option->getIsCorrect(),
        ]);
      }

      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();

      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }
  public function deleteOptions(array $options)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM options WHERE optionid = :optionid';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':optionid' => $option->getOptionId(),
        ]);
      }

      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();

      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveNewOptions(int $questionId, array $options)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'INSERT INTO options (questionid, text, iscorrect) VALUES (:questionid, :text, :iscorrect)';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':questionid' => $questionId,
          ':text' => $option->getText(),
          ':iscorrect' => $option->getIsCorrect(),
        ]);
      }

      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();

      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveOptions(array $options)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'INSERT INTO options (questionid, text, iscorrect) VALUES (:questionid, :text, :iscorrect)';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':questionid' => $option->getOptionId(),
          ':text' => $option->getText(),
          ':iscorrect' => $option->getIsCorrect(),
        ]);
      }

      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();

      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function deleteAllOptions(int $questionId)
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM options WHERE questionid = :questionid';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':questionid' => $questionId,
      ]);


      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();

      throw new Exception("Transaction failed: " . $e->getMessage());
    }
  }
}