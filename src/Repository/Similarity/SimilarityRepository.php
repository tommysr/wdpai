<?php

namespace App\Repository\Similarity;

use App\Repository\Similarity\ISimilarityRepository;
use App\Repository\Repository;

class SimilarityRepository extends Repository implements ISimilarityRepository
{
  public function saveSimilarityMatrix(array $similarityMatrix): void
  {
    $stmt = $this->db->connect()->prepare('INSERT INTO similarities (user_id_1, user_id_2, similarity_score) VALUES (:user_id_1, :user_id_2, :similarity)');
    foreach ($similarityMatrix as $userId1 => $similarities) {
      if ($userId1 == 0) continue; // Skip the first user (it's the admin user
      foreach ($similarities as $userId2 => $similarity) {
        if ($userId2 == 0) continue; // Skip the first user (it's the admin user
        $stmt->execute([
          ':user_id_1' => $userId1,
          ':user_id_2' => $userId2,
          ':similarity' => $similarity
        ]);
      }
    }
  }

  public function deleteSimilarityMatrix(): void {
    $stmt = $this->db->connect()->prepare('DELETE FROM similarities');
    $stmt->execute();
  }
  

  public function getSimilarityMatrix(): array
  {
    $stmt = $this->db->connect()->prepare('SELECT user_id_1, user_id_2, similarity_score FROM similarities');
    $stmt->execute();
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $similarityMatrix = [];
    foreach ($rows as $row) {
      $similarityMatrix[$row['user_id_1']][$row['user_id_2']] = (float) $row['similarity_score'];
    }

    return $similarityMatrix;
  }
}