<?php

namespace App\Services\Register;


use App\Repository\IUserRepository;
use App\Services\Register\IRegisterResult;
use App\Repository\UserRepository;
use App\Request\IFullRequest;
use App\Services\Register\DBRegisterResult;
use App\Models\User;


class DbRegisterStrategy implements IRegisterStrategy
{
  private IUserRepository $userRepository;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository = null)
  {
    $this->userRepository = $userRepository ?: new UserRepository();
    $this->request = $request;
  }

  public function register(): IRegisterResult
  {
    $email = $this->request->getParsedBodyParam('email');
    $password = $this->request->getParsedBodyParam('password');
    $username = $this->request->getParsedBodyParam('username');
    $confirmedPassword = $this->request->getParsedBodyParam('confirmedPassword');

    if ($this->userRepository->userExists($email)) {
      return new DBRegisterResult(['Email exists']);
    }

    if ($this->userRepository->userNameExists($username)) {
      return new DBRegisterResult(['Username already taken']);
    }

    if ($password !== $confirmedPassword) {
      return new DBRegisterResult(['Passwords do not match']);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $user = new User(0, $email, $password_hash, $username);
    $this->userRepository->addUser($user);

    return new DBRegisterResult(['User registered successfully'], true);
  }
}