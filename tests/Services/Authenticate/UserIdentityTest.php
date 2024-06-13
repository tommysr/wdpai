<?php

use PHPUnit\Framework\TestCase;
use App\Services\Authenticate\UserIdentity;
use App\Models\Role;

class UserIdentityTest extends TestCase
{
  public function testGetId()
  {
    $role = new Role('admin', 1);
    $identity = new UserIdentity(123, $role);

    $this->assertEquals(123, $identity->getId());
  }

  public function testGetRole()
  {
    $role = new Role('admin', 1);
    $identity = new UserIdentity(123, $role);

    $this->assertInstanceOf(Role::class, $identity->getRole());
    $this->assertEquals('admin', $identity->getRole()->getName());
  }

  public function testToString()
  {
    $role = new Role('admin', 1);
    $identity = new UserIdentity(123, $role);

    $this->assertEquals('123:admin', $identity->toString());
  }

  public function testFromString()
  {
    $identity = UserIdentity::fromString('123:admin');

    $this->assertInstanceOf(UserIdentity::class, $identity);
    $this->assertEquals(123, $identity->getId());
    $this->assertInstanceOf(Role::class, $identity->getRole());
    $this->assertEquals('admin', $identity->getRole()->getName());
  }
}