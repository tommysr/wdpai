<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../validators/Validator.php';

class AuthService
{
  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository = null)
  {
    $this->userRepository = $userRepository ?: new UserRepository();
  }

  // returns User on success or error string
  public function login(string $email, string $password): User|string {
    if (!Validator::validateEmail($email)) {
      return 'invalid email address';
    }

    $user = $this->userRepository->getUser($email);

    if (!$user || !password_verify($password, $user->getPassword())) {
      return 'invalid password';
    }

    return $user;
  }

  // returns User on success or error string
  public function register(string $email, string $username, string $password, string $confirmedPassword): User|string
  {
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
        return $rule['errorMessage'];
      }
    }

    if ($this->userRepository->userExists($email)) {
      return 'This email is already registered';
    }

    if ($this->userRepository->userNameExists($username)) {
      return 'Username is already taken';
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $user = new User($email, $password_hash, $username, null);
    $this->userRepository->addUser($user);

    return $user;
  }
}