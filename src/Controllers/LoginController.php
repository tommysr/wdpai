<?php

interface IAuthenticateController
{
  public function login(): void;
  public function logout(): void;
}

class LoginControllerImpl extends AppController implements IAuthenticateController
{
  private IAuthenticate $authenticator;
  private SessionService $sessionService;

  public function __construct(IAuthenticate $authenticator = null, ISessionService $sessionService = null)
  {
    $this->authenticator = $authenticator ?: new Authenticator();
    $this->sessionService = $sessionService ?: new SessionService();
  }

  private function renderLoginView(string $message = '')
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => $message]);
  }

  public function login(): void
  {
    if (!$this->request->isPost()) {
      return $this->renderLoginView();
    }

    $email = $this->request->post('email');
    $password = $this->request->post('password');
    $result = $this->authenticator->login($email, $password);

    if ($result instanceof User) {
      $this->sessionService->set(
        'user',
        [
          'id' => $result->getId(),
          'role' => $result->getRole(),
          'username' => $result->getName(),
        ]
      );

      Redirector::redirectTo('/');
    } else {
      $this->renderLoginView("incorrect email or password");
    }
  }

  public function logout(): void
  {
    $this->sessionService->end();
    Redirector::redirectTo('/');
  }
}