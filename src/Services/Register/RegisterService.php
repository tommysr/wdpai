<?php

namespace App\Services\Register;

use App\Services\Register\IRegisterService;
use App\Repository\IUserRepository;
use App\Services\Register\IRegisterResult;
use App\Validator\IValidationChain;
use App\Repository\UserRepository;
use App\Validator\EmailValidationRule;
use App\Validator\UsernameFormatValidationRule;
use App\Validator\ConfirmedPasswordValidationRule;
use App\Validator\MinLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Request\IFullRequest;
use App\Services\Register\DBRegisterResult;
use App\Models\User;

class RegisterService implements IRegisterService
{
  private IUserRepository $userRepository;
  private IValidationChain $validationChain;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository = null, IValidationChain $validationChain = null)
  {
    $this->userRepository = $userRepository ?: new UserRepository();
    $this->request = $request;

    if ($validationChain) {
      $this->validationChain = $validationChain;
    } else {
      $this->validationChain = new $validationChain();
      $this->validationChain->addRule('email', new EmailValidationRule());
      $this->validationChain->addRule('email', new RequiredValidationRule());

      $this->validationChain->addRule('password', new MinLengthValidationRule(8));
      $this->validationChain->addRule('password', new RequiredValidationRule());

      $this->validationChain->addRule('username', new RequiredValidationRule());
      $this->validationChain->addRule('username', new UsernameFormatValidationRule());

      $this->validationChain->addRule('confirmedPassword', new ConfirmedPasswordValidationRule($this->request->getParsedBodyParam('password')));
    }
  }

  public function register(array $data): IRegisterResult
  {
    $errors = $this->validationChain->validateFields($this->request->getParsedBody());

    if (!empty($errors)) {
      return new DBRegisterResult($errors, false);
    }

    $email = $this->request->getParsedBodyParam('email');
    $password = $this->request->getParsedBodyParam('password');
    $username = $this->request->getParsedBodyParam('username');

    if ($this->userRepository->userExists($email)) {
      return new DBRegisterResult(['Email already exists'], false);
    }

    if ($this->userRepository->userNameExists($username)) {
      return new DBRegisterResult(['Username already exists'], false);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $user = new User($email, $password_hash, $username, null);
    $this->userRepository->addUser($user);

    return new DBRegisterResult(['User registered successfully'], true);
  }
}