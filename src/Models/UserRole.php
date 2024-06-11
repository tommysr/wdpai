<?php

namespace App\Models;


enum UserRole: string
{
  case ADMIN = 'admin';
  case NORMAL = 'normal';
  case GUEST = 'guest';
  case CREATOR = 'creator';
}