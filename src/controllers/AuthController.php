<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../validators/Validator.php';
require_once __DIR__ . '/../middleware/AuthInterceptor.php';


class AuthController extends AppController
{
  private AuthService $authService;


  public function __construct()
  {
    parent::__construct();
    $this->authService = new AuthService();
  }

  public function logout()
  {
    $this->sessionService->end();
    $this->redirect('/');
  }

  public function login()
  {
    if (AuthInterceptor::isLoggedIn()) {
      return $this->redirect("/");
    }

    if (!$this->request->isPost()) {
      return $this->renderLoginView();
    }

    $email = $this->request->post('email');
    $password = $this->request->post('password');
    $result = $this->authService->login($email, $password);


    if ($result instanceof User) {
      $this->sessionService->set(
        'user',
        [
          'id' => $result->getId(),
          'role' => $result->getRole(),
          'username' => $result->getName(),
        ]
      );
      $this->redirect('/');
    } else {
      $this->renderLoginView("incorrect email or password");
    }
  }


  public function register()
  {
    if (AuthInterceptor::isLoggedIn()) {
      return $this->redirect("/");
    }

    if (!$this->request->isPost()) {
      return $this->renderRegisterView();
    }

    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmedPassword = $_POST['confirmedPassword'];

    $result = $this->authService->register($email, $username, $password, $confirmedPassword);

    if (is_string($result)) {
      return $this->renderRegisterView($result);
    }

    $this->renderLoginView();
  }

  private function renderRegisterView(string $message = '')
  {
    return $this->render('register', ['title' => 'Sign up', 'message' => $message]);
  }

  private function renderLoginView(string $message = '')
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => $message]);
  }
}