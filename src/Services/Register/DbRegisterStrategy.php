<?php

namespace App\Services\Register;


use App\Models\UserRole;
use App\Repository\IUserRepository;
use App\Repository\Role\IRoleRepository;
use App\Result\IResult;
use App\Result\Result;
use App\Request\IFullRequest;
use App\Models\User;

class DbRegisterStrategy implements IRegisterStrategy
{
  private IUserRepository $userRepository;
  private IRoleRepository $roleRepository;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository, IRoleRepository $roleRepository)
  {
    $this->userRepository = $userRepository;
    $this->roleRepository = $roleRepository;
    $this->request = $request;
  }

  private function validateRegistrationData($email, $username, $password, $confirmedPassword): IResult
  {
    if ($this->userRepository->getUserByEmail($email)) {
      return new Result(['Email exists']);
    }

    if ($this->userRepository->getUserByName($username)) {
      return new Result(['Username already taken']);
    }

    if ($password !== $confirmedPassword) {
      return new Result(['Passwords do not match']);
    }

    return new Result([], true);
  }

  public function register(): IResult
  {
    $email = $this->request->getParsedBodyParam('email');
    $password = $this->request->getParsedBodyParam('password');
    $username = $this->request->getParsedBodyParam('username');
    $confirmedPassword = $this->request->getParsedBodyParam('confirmedPassword');

    $result = $this->validateRegistrationData($email, $username, $password, $confirmedPassword);

    if (!$result->isValid()) {
      return $result;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $defaultRole = $this->roleRepository->getRole(UserRole::NORMAL->value);
    $user = new User(0, $email, $password_hash, $username, $defaultRole);
    $this->userRepository->addUser($user);

    return new Result(['User registered successfully'], true);
  }
}