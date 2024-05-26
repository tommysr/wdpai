<?php

namespace App\Repository;

use App\Models\Role;
use App\Repository\IUserRepository;
use App\Models\Interfaces\IUser;
use App\Models\User;
use App\Repository\Repository;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
  public function getAllUserIds(): array
  {
    $sql = 'SELECT user_id FROM users';
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute();
    $userIds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(function ($userId) {
      return $userId['user_id'];
    }, $userIds);
  }

  public function addUser(IUser $user): void
  {
    // default role_id and avatar_id are 0
    $stmt = $this->db->connect()->prepare('
      INSERT INTO Users (email, username, password, join_date, role_id, avatar_id)
      VALUES (?, ?, ?, ?, ?, 1)
    ');

    $stmt->execute([
      $user->getEmail(),
      $user->getName(),
      $user->getPassword(),
      $user->getJoinDate(),
      $user->getRole()->getId()
    ]);
  }

  private function constructUser(array $data): IUser
  {
    $role = new Role($data['role_name'], $data['role_id']);

    return new User(
      $data['user_id'],
      $data['email'],
      $data['password'],
      $data['username'],
      $role,
      $data['join_date'],
      $data['avatar_url']
    );
  }

  private function getUserQuery(string $whereClause = ''): string
  {
    $sql = "SELECT user_id, email, password, username, roles.name as role_name, join_date, picture_url as avatar_url FROM users JOIN roles ON users.role_id = roles.role_id JOIN pictures ON users.avatar_id = pictures.picture_id ";
    $sql .= $whereClause;

    return $sql;
  }

  public function getUserById(int $id): ?IUser
  {
    $sql = $this->getUserQuery("WHERE user_id = :id");
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return null;
    }

    return $this->constructUser($user);
  }

  public function getUser(string $email): ?IUser
  {
    $sql = $this->getUserQuery("WHERE email = :email");
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return null;
    }

    return $this->constructUser($user);
  }


  public function userExists(string $email): bool
  {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      return true;
    } else {
      return false;
    }
  }

  public function userNameExists(string $username): bool
  {
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
      return true;
    } else {
      return false;
    }
  }
}