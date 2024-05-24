<?php

namespace App\Repository\Rating;

use App\Models\Interfaces\IRating;
use App\Models\Rating;
use App\Repository\Repository;
use App\Repository\Rating\IRatingRepository;

class RatingRepository extends Repository implements IRatingRepository
{
  public function getRatings(): array
  {
    $ratings = [];
    $pdo = $this->db->connect();
    $query = "SELECT * FROM ratings";
    $smtp = $pdo->prepare($query);
    $smtp->execute();

    while ($row = $smtp->fetchAll(\PDO::FETCH_ASSOC)) {
      $ratings[] = new Rating($row['user_id'], $row['quest_id'], $row['rating']);
    }
    return $ratings;
  }

  public function getRating(int $userId, int $questId): ?IRating
  {
    $pdo = $this->db->connect();
    $query = "SELECT * FROM ratings WHERE user_id = :userId AND quest_id = :questId";
    $smtp = $pdo->prepare($query);
    $smtp->execute([
      ':userId' => $userId,
      ':questId' => $questId,
    ]);
    $row = $smtp->fetch(\PDO::FETCH_ASSOC);
    if ($row) {
      return new Rating($row['user_id'], $row['quest_id'], $row['rating']);
    }
    return null;
  }

  public function addRating(IRating $rating): void
  {
    $pdo = $this->db->connect();
    $query = "INSERT INTO ratings (user_id, quest_id, rating) VALUES (:userId, :questId, :rating)";
    $smtp = $pdo->prepare($query);
    $smtp->execute([
      ':userId' => $rating->getUserId(),
      ':questId' => $rating->getQuestId(),
      ':rating' => $rating->getRating(),
    ]);
  }

  public function updateRating(IRating $rating): void
  {
    $pdo = $this->db->connect();
    $query = "UPDATE ratings SET rating = :rating WHERE user_id = :userId AND quest_id = :questId";
    $smtp = $pdo->prepare($query);
    $smtp->execute([
      ':userId' => $rating->getUserId(),
      ':questId' => $rating->getQuestId(),
      ':rating' => $rating->getRating(),
    ]);
  }

  public function deleteRating(int $userId, int $questId): void
  {
    $pdo = $this->db->connect();
    $query = "DELETE FROM ratings WHERE user_id = :userId AND quest_id = :questId";
    $smtp = $pdo->prepare($query);
    $smtp->execute([
      ':userId' => $userId,
      ':questId' => $questId,
    ]);
  }
}