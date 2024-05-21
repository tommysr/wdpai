<?php

namespace App\Services\Authorize;

use App\Services\Authorize\IAcl;
use App\Services\Authorize\IRole;


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

    public function isAllowed(IRole $role, string $resource, string $action): bool
    {
        $roleName = $role->getName();
        return isset($this->permissions[$roleName][$resource][$action])
            ? $this->permissions[$roleName][$resource][$action]
            : false;
    }

    public function addRole(IRole $role): void
    {
        $roleName = $role->getName();
        if (!isset($this->roles[$roleName])) {
            $this->roles[$roleName] = $role;
            $this->permissions[$roleName] = [];
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

    public function allow(IRole $role, string $resource, string $action): void
    {
        $this->addRole($role);
        $this->addResource($resource);
        $this->addAction($action);

        $roleName = $role->getName();
        if (!isset($this->permissions[$roleName][$resource])) {
            $this->permissions[$roleName][$resource] = [];
        }
        $this->permissions[$roleName][$resource][$action] = true;
    }

    public function deny(IRole $role, string $resource, string $action): void
    {
        $roleName = $role->getName();
        if (isset($this->permissions[$roleName][$resource][$action])) {
            $this->permissions[$roleName][$resource][$action] = false;
        }
    }
}
