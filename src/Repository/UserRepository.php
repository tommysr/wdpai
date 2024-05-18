<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

interface IUserRepository
{
  public function addUser(User $user): void;
  public function getUser(string $email): ?User;
  public function getUserById(int $id): ?User;
  public function userExists($email): bool;
  public function userNameExists($username): bool;
}

class UserRepository extends Repository implements IUserRepository
{
  public function __construct(IDatabase $db = null, IDatabaseConfig $config = null)
  {
    parent::__construct($db, $config);
  }

  public function addUser(User $user): void
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

  public function getUserById(int $id): ?User
  {
    $stmt = $this->db->connect()->prepare('
      SELECT * FROM Users WHERE UserID = :id
    ');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return null;
    }

    return new User($user['userid'], $user['email'], $user['password'], $user['username'], $user['role'], $user['joindate']);
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

    return new User($user['userid'], $user['email'], $user['password'], $user['username'], $user['role'], $user['joindate']);
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
}