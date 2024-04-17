<?php

require_once "AppController.php";
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../validators/Validator.php';


class AuthController extends AppController
{
  private UserRepository $userRepository;


  public function __construct()
  {
    parent::__construct();
    $this->userRepository = new UserRepository();
  }


  public function login()
  {
    if (!$this->isPost()) {
      return $this->render('login', ['title' => 'Sign in']);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!Validator::validateEmail($email)) {
      return $this->renderLoginView('incorrect email');
    }

    $user = $this->userRepository->getUser($email);

    if (!$user) {
      return $this->renderLoginView('you are not registered');
    }

    if (!password_verify($password, $user->getPassword())) {
      return $this->renderLoginView('incorrect password');
    }

    $url = "http://$_SERVER[HTTP_HOST]";
    header("Location: {$url}/quests");
  }

  public function register()
  {
    if (!$this->isPost()) {
      return $this->renderRegisterView();
    }

    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmedPassword = $_POST['confirmedPassword'];
    $repository = $this->userRepository;

    $validationRules = [
      ['validator' => 'validateEmail', 'value' => $email, 'errorMessage' => 'Invalid email'],
      ['validator' => 'validatePassword', 'value' => $password, 'errorMessage' => 'Use a strong password (at least 8 characters)'],
      ['validator' => 'validateUsername', 'value' => $username, 'errorMessage' => 'Invalid username'],
      ['validator' => 'validateConfirmedPassword', 'value' => ['password' => $password, 'confirmedPassword' => $confirmedPassword], 'errorMessage' => 'Passwords do not match']
    ];

    foreach ($validationRules as $rule) {
      $validator = $rule['validator'];
      $value = $rule['value'];

      if (!Validator::$validator($value)) {
        return $this->renderRegisterView($rule['errorMessage']);
      }
    }

    if ($repository->userExists($email)) {
      return $this->renderRegisterView('This email is already registered');
    }

    if ($repository->userNameExists($username)) {
      return $this->renderRegisterView('Username is already taken');
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $user = new User($email, $password_hash, $username, null);
    $this->userRepository->addUser($user);

    return $this->render('login', ['title' => 'Sign in', 'message' => 'registered']);
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