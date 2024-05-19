<?php
namespace App\Services\Authenticate;

use App\Models\User;
use App\Services\Authenticate\IAuthenticate;
use App\Repository\IUserRepository;
use App\Validator\IValidationChain;
use App\Repository\UserRepository;
use App\Validator\Validator;
use App\Validator\EmailValidationRule;
use App\Validator\PasswordLengthValidationRule;


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
      throw new \Exception('Invalid login data');
    }

    if (!$this->validationChain->validateField('password', $password)) {
      throw new \Exception('Invalid login data');
    }
  }

  public function login(string $email, string $password): User
  {
    $this->validateLoginParams($email, $password);

    $user = $this->userRepository->getUser($email);

    if (!$user || !password_verify($password, $user->getPassword())) {
      throw new \Exception('Invalid login data');
    }

    return $user;
  }
}
