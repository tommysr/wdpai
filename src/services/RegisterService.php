<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../validators/Validator.php';
require_once __DIR__ . '/../exceptions/User.php';

interface IRegister
{
  public function register(string $email, string $username, string $password, string $confirmedPassword): User;
}

class Registerer implements IRegister
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
      $this->validationChain->addRule('username', new UsernameFormatValidationRule());
    }
  }

  public function register(string $email, string $username, string $password, string $confirmedPassword): User
  {
    if ($this->validationChain->getRules('confirmedPassword') === []) {
      $this->validationChain->addRule('confirmedPassword', new ConfirmedPasswordValidationRule($password));
    }

    $this->validationChain->validateFields([
      'email' => $email,
      'password' => $password,
      'username' => $username,
      'confirmedPassword' => $confirmedPassword
    ]);

    if ($this->userRepository->userExists($email)) {
      throw new AlreadyRegistered();
    }

    if ($this->userRepository->userNameExists($username)) {
      throw new UsernameTaken();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $user = new User($email, $password_hash, $username, null);
    $this->userRepository->addUser($user);

    return $user;
  }
}


//   private function validateRegisterParams(string $email, string $password, string $username, string $confirmedPassword): void
//   {
//     // $validationRules = [
//     //   ['validator' => 'validateEmail', 'value' => $email, 'errorMessage' => 'Invalid email'],
//     //   ['validator' => 'validatePassword', 'value' => $password, 'errorMessage' => 'Use a strong password (at least 8 characters)'],
//     //   ['validator' => 'validateUsername', 'value' => $username, 'errorMessage' => 'Invalid username'],
//     //   ['validator' => 'validateConfirmedPassword', 'value' => ['password' => $password, 'confirmedPassword' => $confirmedPassword], 'errorMessage' => 'Passwords do not match']
//     // ];

//     // foreach ($validationRules as $rule) {
//     //   $validator = $rule['validator'];
//     //   $value = $rule['value'];

//     //   if (!Validator::$validator($value)) {
//     //     throw new ValidationException($rule['errorMessage']);
//     //   }
//     // }
//   }