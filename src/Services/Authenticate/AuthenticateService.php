<?php
namespace App\Services\Authenticate;

use App\Services\Session\ISessionService;
use App\Services\Authenticate\IAuthService;

class AuthenticateService implements IAuthService
{
  private ISessionService $session;

  public function __construct(ISessionService $session)
  {
    $this->session = $session;
  }

  public function authenticate(IAuthAdapter $adapter): IAuthResult
  {
    $result = $adapter->authenticate();
    $this->session->set('identity', $result->getIdentity());
    return $result;
  }

  public function hasIdentity(): bool
  {
    return $this->session->has('identity');
  }

  public function getIdentity(): string
  {
    return $this->session->get('identity');
  }

  public function clearIdentity()
  {
    $this->session->delete('identity');
  }
}

