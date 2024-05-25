<?php

namespace App\Models\Interfaces;

interface IRole
{
  public function getName(): string;
  public static function fromName(string $role): self;
}