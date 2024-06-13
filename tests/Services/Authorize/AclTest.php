<?php

use PHPUnit\Framework\TestCase;
use App\Services\Authorize\Acl;

class AclTest extends TestCase
{
  public function testIsAllowedReturnsTrueWhenPermissionExists()
  {
    $acl = new Acl(['admin'], ['resource'], ['action']);
    $acl->allow('admin', 'resource', 'action');

    $this->assertTrue($acl->isAllowed('admin', 'resource', 'action'));
  }

  public function testIsAllowedReturnsFalseWhenPermissionDoesNotExist()
  {
    $acl = new Acl(['admin'], ['resource'], ['action']);

    $this->assertFalse($acl->isAllowed('admin', 'resource', 'action'));
  }

  public function testAllowAddsPermission()
  {
    $acl = new Acl(['admin'], ['resource'], ['action']);
    $acl->allow('admin', 'resource', 'action');

    $this->assertTrue($acl->isAllowed('admin', 'resource', 'action'));
  }

  public function testDenyRemovesPermission()
  {
    $acl = new Acl(['admin'], ['resource'], ['action']);
    $acl->allow('admin', 'resource', 'action');
    $acl->deny('admin', 'resource', 'action');

    $this->assertFalse($acl->isAllowed('admin', 'resource', 'action'));
  }
}