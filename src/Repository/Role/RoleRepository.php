<?php

namespace App\Repository\Role;

use App\Models\Role;
use App\Models\Interfaces\IRole;
use App\Repository\Repository;
use App\Repository\Role\IRoleRepository;

class RoleRepository extends Repository implements IRoleRepository
{

  public function getRole(string $role): IRole
  {
    $sql = "SELECT * FROM roles WHERE role = :role";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->bindParam(':role', $role, \PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch();

    return new Role($result['name'], $result['role_id']);
  }

  public function getRoles(): array
  {
    $sql = "SELECT * FROM roles";

    $stmt = $this->db->connect()->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll();
    $roles = [];

    foreach ($result as $role) {
      $roles[] = new Role($role['name'], $role['role_id']);
    }

    return $roles;
  }
}
