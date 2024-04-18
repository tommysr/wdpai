<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class UserRepository extends Repository
{
  public function addUser(User $user)
  {
    $stmt = $this->db->connect()->prepare('
      INSERT INTO Users (Email, Username, Password, JoinDate)
      VALUES (?, ?, ?, ?)
    ');

    $stmt->execute([
      $user->getEmail(),
      $user->getName(),
      $user->getPassword(),
      $user->getJoinDate()
    ]);
  }

  public function getUser(string $email): ?User
  {
    $stmt = $this->db->connect()->prepare('
      SELECT * FROM USERS WHERE email = :email
    ');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return null;
    }

    return new User($user['email'], $user['password'], $user['username'], $user['joindate']);
  }


  public function userExists($email): bool
  {
    $stmt = $this->db->connect()->prepare('
      SELECT FROM Users WHERE Email = ?;
    ');
    $stmt->execute([
      $email
    ]);

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      return true;
    } else {
      return false;
    }
  }

  public function userNameExists($username): bool
  {
    $stmt = $this->db->connect()->prepare('
    SELECT FROM Users WHERE Username = ?;
  ');
    $stmt->execute([
      $username
    ]);

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      return true;
    } else {
      return false;
    }
  }

  public function hasUserParticipatedInQuest($email, $questId)
  {
    $sql = "SELECT COUNT(*) AS participation_count
              FROM QuestStatistics qs
              INNER JOIN Users u ON qs.UserID = u.UserID
              WHERE u.Email = :email
              AND qs.QuestID = :questId";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute(['email' => $email, 'questId' => $questId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['participation_count'] > 0;
  }
}