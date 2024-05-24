<?php

namespace App\Repository\Similarity;

use App\Repository\Similarity\ISimilarityRepository;
use App\Repository\Repository;

class SimilarityRepository extends Repository implements ISimilarityRepository
{
  public function saveSimilarityMatrix(array $similarityMatrix): void
  {
    $stmt = $this->db->connect()->prepare('INSERT INTO similarity_matrix (user_id_1, user_id_2, similarity) VALUES (:user_id_1, :user_id_2, :similarity)');
    $stmt->execute();
    foreach ($similarityMatrix as $userId1 => $similarities) {
      foreach ($similarities as $userId2 => $similarity) {
        $stmt->execute([
          ':user_id_1' => $userId1,
          ':user_id_2' => $userId2,
          ':similarity' => $similarity
        ]);
      }
    }
  }

  public function getSimilarityMatrix(): array
  {
    $stmt = $this->db->connect()->prepare('SELECT user_id_1, user_id_2, similarity FROM similarity_matrix');
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $similarityMatrix = [];
    foreach ($rows as $row) {
      $similarityMatrix[$row['user_id_1']][$row['user_id_2']] = $row['similarity'];
    }

    return $similarityMatrix;
  }
}