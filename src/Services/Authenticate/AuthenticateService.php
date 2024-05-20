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

// class Authenticator implements IAuthenticate
// {
//   private IUserRepository $userRepository;
//   private IValidationChain $validationChain;

//   public function __construct(?IUserRepository $userRepository = null, ?IValidationChain $validationChain = null)
//   {
//     $this->userRepository = $userRepository ?: new UserRepository();

//     if ($validationChain) {
//       $this->validationChain = $validationChain;
//     } else {
//       $this->validationChain = new Validator();
//       $this->validationChain->addRule('email', new EmailValidationRule());
//       $this->validationChain->addRule('password', new PasswordLengthValidationRule());
//     }
//   }

//   private function validateLoginParams(string $email, string $password): void
//   {

//     if (!$this->validationChain->validateField('email', $email)) {
//       throw new \Exception('Invalid login data');
//     }

//     if (!$this->validationChain->validateField('password', $password)) {
//       throw new \Exception('Invalid login data');
//     }
//   }

//   public function login(string $email, string $password): User
//   {
//     $this->validateLoginParams($email, $password);

//     $user = $this->userRepository->getUser($email);

//     if (!$user || !password_verify($password, $user->getPassword())) {
//       throw new \Exception('Invalid login data');
//     }

//     return $user;
//   }
// }
