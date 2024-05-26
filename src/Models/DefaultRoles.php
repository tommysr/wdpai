<?php

namespace App\Models;

enum UserRole
{
  case ADMIN = 'admin';
  case NORMAL = 'normal';
  case GUEST = 'guest';
  case CREATOR = 'creator';

  public function __toString(): string
  {
    return match ($this) {
      self::ADMIN => 'admin',
      self::NORMAL => 'normal',
      self::GUEST => 'guest',
      self::CREATOR => 'creator',
    };
  }
}