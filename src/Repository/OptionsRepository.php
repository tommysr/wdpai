<?php

namespace App\Repository;

use App\Models\IOption;
use App\Repository\Repository;
use App\Models\Option;
use App\Repository\IOptionsRepository;

class OptionsRepository extends Repository implements IOptionsRepository
{
  public function getCorrectOptionsIdsForQuestionId(int $questionId): array
  {
    $options = [];
    $sql = "SELECT option_id FROM options WHERE question_id = :question_id AND is_correct = true";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute([':question_id' => $questionId]);
    $optionsFetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($optionsFetched as $option) {
      $options[] = $option['option_id'];
    }

    return $options;
  }

  private function createOption(array $option): IOption
  {
    return new Option($option['option_id'], $option['question_id'], $option['text'], $option['is_correct']);
  }

  public function getOptionsByQuestionId(int $questionId): array
  {
    $options = [];
    $sql = "SELECT * FROM options WHERE question_id = :question_id";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['question_id' => $questionId]);
    $optionsFetched = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($optionsFetched as $option) {
      $options[] = $this->createOption($option);
    }

    return $options;
  }

  public function updateOptions(array $options): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'UPDATE options SET question_id = :question_id, text = :text, is_correct = :is_correct WHERE option_id = :option_id';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':question_id' => $option->getQuestionId(),
          ':text' => $option->getText(),
          ':is_correct' => $option->getIsCorrect(),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }
  public function deleteOptions(array $options): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM options WHERE option_id = :option_id';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':option_id' => $option->getOptionId(),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveNewOptions(int $questionId, array $options): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'INSERT INTO options (question_id, text, is_correct) VALUES (:question_id, :text, :is_correct)';
      $stmt = $pdo->prepare($sql);

      foreach ($options as $option) {
        $stmt->execute([
          ':question_id' => $questionId,
          ':text' => $option->getText(),
          ':is_correct' => $option->getIsCorrect(),
        ]);
      }

      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function deleteOptionById(int $optionId)
  {
    $pdo = $this->db->connect();

    $sql = 'DELETE FROM options WHERE option_id = :optionId';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      ':optionId' => $optionId,
    ]);
  }


  public function deleteOption(IOption $option)
  {
    $pdo = $this->db->connect();

    $sql = 'DELETE FROM options WHERE option_id = :optionId';
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      ':optionId' => $option->getOptionId(),
    ]);
  }

  public function deleteAllOptions(int $questionId): void
  {
    $pdo = $this->db->connect();
    $pdo->beginTransaction();

    try {
      $sql = 'DELETE FROM options WHERE question_id = :question_id';
      $stmt = $pdo->prepare($sql);

      $stmt->execute([
        ':question_id' => $questionId,
      ]);


      $pdo->commit();
    } catch (\PDOException $e) {
      $pdo->rollBack();

      throw new \Exception("Transaction failed: " . $e->getMessage());
    }
  }

  public function saveOption(IOption $option): int
  {
    $sql = 'INSERT INTO options (question_id, text, is_correct) VALUES (:question_id, :text, :is_correct)';
    $pdo = $this->db->connect();
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
      ':question_id' => $option->getQuestionId(),
      ':text' => $option->getText(),
      ':is_correct' => $option->getIsCorrect() ? 1 : 0,
    ]);

    return $pdo->lastInsertId();
  }
}
