<?php

interface Middleware
{
  /**
   * Handle the middleware logic.
   *
   * @return void
   */
  public function handle(): void;

  /**
   * Set the next middleware in the chain.
   *
   * @param Middleware $middleware The next middleware in the chain.
   * @return Middleware Returns the next middleware.
   */
  public function setNext(Middleware $middleware): Middleware;
}

class UserAuthorizationMiddleware implements Middleware
{
  private UserAuthorizer $authorizer;
  protected Role $role;
  private ?Middleware $next = null;

  public function __construct(UserAuthorizer $authorizer, Role $role)
  {
    $this->authorizer = $authorizer;
    $this->role = $role;
  }

  public function setNext(Middleware $middleware): Middleware
  {
    $this->next = $middleware;
    return $middleware;
  }

  public function handle(): void
  {
    try {
      $this->authorizer->authorize($this->role);
    } catch (AuthorizationException $e) {
      Redirector::redirectTo('/unauthorized');
    }

    if ($this->next !== null) {
      $this->next->handle();
    }
  }
}


interface UserAuthorizer
{
  /**
   * Authorize a user based on the required role.
   *
   * @param Role $roleRequired The required role for authorization.
   * @return bool Returns true if the user is authorized; otherwise, false.
   * @throws AuthorizationException Thrown when the user is not authorized.
   */
  public function authorize(Role $roleRequired): void;
}


class RoleAuthorizationService implements UserAuthorizer
{
  private ISessionService $sessionService;
  private int $userId;
  private Role $role;

  // Dependency injection of the session service.
  public function __construct(ISessionService $sessionService)
  {
    $this->sessionService = $sessionService;
    $this->initializeUserData();
  }

  /**
   * Initialize user data from session.
   *
   * @return void
   */
  private function initializeUserData(): void
  {
    $globalVariables = GlobalVariablesManager::getGlobalVariables($this->sessionService);
    $this->userId = $globalVariables['user']['id'] ?? null;
    $this->role = $globalVariables['role'] ?? null;
  }

  /**
   * Authorize a user based on the required role.
   *
   * @param Role $roleRequired The required role for authorization.
   * @throws AuthorizationException Thrown when the user is not authorized.
   */
  public function authorize(Role $roleRequired): void
  {
    if ($this->role === null) {
      throw new AuthorizationException('User not logged in');
    }
  }
}
