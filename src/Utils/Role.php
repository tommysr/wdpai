<?php


enum Role
{
  case CREATOR;
  case ADMIN;
  case USER;
  case GUEST;
}

function getRoleFromString(string $roleString): Role
{
  switch ($roleString) {
    case 'normal':
      return Role::USER;
    case 'creator':
      return Role::CREATOR;
    case 'admin':
      return Role::ADMIN;
  }

  return Role::GUEST;
}




