<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAcl;
use App\Models\Interfaces\IRole;


class Acl implements IAcl
{
    private array $roles = [];
    private array $resources = [];
    private array $actions = [];
    private array $permissions = [];

    public function __construct(array $roles = [], array $resources = [], array $actions = [])
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    public function isAllowed(string $role, string $resource, string $action): bool
    {
        return isset($this->permissions[$role][$resource][$action])
            ? $this->permissions[$role][$resource][$action]
            : false;
    }

    public function addRole(string $role): void
    {
        if (!isset($this->roles[$role])) {
            $this->roles[$role] = $role;
            $this->permissions[$role] = [];
        }
    }

    public function addResource(string $resource): void
    {
        if (!in_array($resource, $this->resources)) {
            $this->resources[] = $resource;
        }
    }

    public function addAction(string $action): void
    {
        if (!in_array($action, $this->actions)) {
            $this->actions[] = $action;
        }
    }

    public function allow(string $role, string $resource, string $action): void
    {
        $this->addRole($role);
        $this->addResource($resource);
        $this->addAction($action);

        if (!isset($this->permissions[$role][$resource])) {
            $this->permissions[$role][$resource] = [];
        }
        $this->permissions[$role][$resource][$action] = true;
    }

    public function deny(string $role, string $resource, string $action): void
    {
        if (isset($this->permissions[$role][$resource][$action])) {
            $this->permissions[$role][$resource][$action] = false;
        }
    }
}
