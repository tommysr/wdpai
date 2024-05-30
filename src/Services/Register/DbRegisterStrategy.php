<?php

namespace App\Services\Register;


use App\Models\UserRole;
use App\Repository\IUserRepository;
use App\Repository\Role\IRoleRepository;
use App\Repository\Role\RoleRepository;
use App\Services\Register\IRegisterResult;
use App\Repository\UserRepository;
use App\Request\IFullRequest;
use App\Services\Register\DBRegisterResult;
use App\Models\User;


class DbRegisterStrategy implements IRegisterStrategy
{
  private IUserRepository $userRepository;
  private IRoleRepository $roleRepository;
  private IFullRequest $request;

  public function __construct(IFullRequest $request, IUserRepository $userRepository = null, IRoleRepository $roleRepository = null)
  {
    $this->userRepository = $userRepository ?: new UserRepository();
    $this->roleRepository = $roleRepository ?: new RoleRepository();
    $this->request = $request;
  }

  private function validateRegistrationData($email, $username, $password, $confirmedPassword): IRegisterResult
  {
    if ($this->userRepository->userExists($email)) {
      return new DBRegisterResult(['Email exists']);
    }

    if ($this->userRepository->userNameExists($username)) {
      return new DBRegisterResult(['Username already taken']);
    }

    if ($password !== $confirmedPassword) {
      return new DBRegisterResult(['Passwords do not match']);
    }

    return new DBRegisterResult([], true);
  }

  public function register(): IRegisterResult
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

    return new DBRegisterResult(['User registered successfully'], true);
  }
}