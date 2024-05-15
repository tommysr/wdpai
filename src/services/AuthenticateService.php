<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../validators/Validator.php';
require_once __DIR__ . '/../exceptions/User.php';

interface IAuthenticate
{
  public function login(string $email, string $password): User;
}

class Authenticator implements IAuthenticate
{
  private IUserRepository $userRepository;
  private IValidationChain $validationChain;

  public function __construct(?IUserRepository $userRepository = null, ?IValidationChain $validationChain = null)
  {
    $this->userRepository = $userRepository ?: new UserRepository();

    if ($validationChain) {
      $this->validationChain = $validationChain;
    } else {
      $this->validationChain = new Validator();
      $this->validationChain->addRule('email', new EmailValidationRule());
      $this->validationChain->addRule('password', new PasswordLengthValidationRule());
    }
  }

  private function validateLoginParams(string $email, string $password): void
  {

    if (!$this->validationChain->validateField('email', $email)) {
      throw new ValidationException('Invalid login data');
    }

    if (!$this->validationChain->validateField('password', $password)) {
      throw new ValidationException('Invalid login data');
    }
  }

  public function login(string $email, string $password): User
  {
    $this->validateLoginParams($email, $password);

    $user = $this->userRepository->getUser($email);

    if (!$user || !password_verify($password, $user->getPassword())) {
      throw new ValidationException('Invalid login data');
    }

    return $user;
  }
}
