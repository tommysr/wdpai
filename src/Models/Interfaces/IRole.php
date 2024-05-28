<?php

namespace App\Models\Interfaces;

interface IRole
{
  public function getName(): string;
  public function getId(): int;
}