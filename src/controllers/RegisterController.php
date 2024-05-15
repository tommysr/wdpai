<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../validators/Validator.php';
require_once __DIR__ . '/../middleware/AuthInterceptor.php';

interface IRegisterController
{
  public function register(): void;
}


class RegisterController extends AppController implements IRegisterController
{
  private IRegister $registerer;


  public function __construct(IRegister $registerer = null)
  {
    parent::__construct();
    $this->registerer = $registerer ?: new Registerer();
  }


  public function register(): void
  {
    if (!$this->request->isPost()) {
      return $this->renderRegisterView();
    }

    $email = $this->request->post('email');
    $username = $this->request->post('username');
    $password = $this->request->post('password');
    $confirmedPassword = $this->request->post('confirmedPassword');

    $result = $this->registerer->register($email, $username, $password, $confirmedPassword);

    if (is_string($result)) {
      return $this->renderRegisterView($result);
    }

    Redirector::redirectTo('/login');
  }

  private function renderRegisterView(string $message = '')
  {
    return $this->render('register', ['title' => 'Sign up', 'message' => $message]);
  }
}