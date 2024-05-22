<?php

namespace App\Services\Authenticate;

use App\Services\Authenticate\IAuthAdapter;
use App\Services\Authenticate\IAuthResult;
use App\Repository\IUserRepository;


use App\Services\Authenticate\DBAuthResult;
use App\Repository\UserRepository;


class DBAuthAdapter implements IAuthAdapter
{
    private string $email;
    private string $password;
    private IUserRepository $userRepository;

    public function __construct(string $email, string $password, ?IUserRepository $userRepository = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->userRepository = $userRepository ?: new UserRepository();
    }

    public function authenticate(): IAuthResult
    {
        $user = $this->userRepository->getUser($this->email);

        if (!$user || !password_verify($this->password, $user->getPassword())) {
            return new DBAuthResult(null, [''], false);
        }

        $identity = new UserIdentity($user->getId(), $user->getRole());
 
        return new DBAuthResult($identity, ['Authenticated'], true);
    }
}
