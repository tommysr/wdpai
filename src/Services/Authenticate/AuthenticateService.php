<?php
namespace App\Services\Authenticate;

use App\Services\Session\ISessionService;
use App\Services\Authenticate\IAuthService;
use App\Services\Authenticate\IIdentity;
use App\Services\Authenticate\UserIdentity;

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
    if ($result->isValid()) {
      $this->saveIdentity($result->getIdentity());
    }
    return $result;
  }

  public function saveIdentity(IIdentity $identity)
  {
    $this->session->set('identity', $identity->toString());
  }

  public function hasIdentity(): bool
  {
    return $this->session->has('identity');
  }

  public function getIdentity(): IIdentity
  {
    $identityString = $this->session->get('identity');
    
    return UserIdentity::fromString($identityString);
  }

  public function clearIdentity()
  {
    $this->session->delete('identity');
  }
}

