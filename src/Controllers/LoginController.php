<?php

namespace App\Controllers;

use App\Request\IRequest;
use App\Middleware\IResponse;
use App\Middleware\RedirectResponse;


class LoginController extends AppController implements ILoginController
{
  public function index(IRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function login(IRequest $request): IResponse
  {
    return $this->render('login', ['title' => 'Sign in', 'message' => '']);
  }

  public function logout(IRequest $request): IResponse
  {
    return new RedirectResponse('/login');
  }
}



//   $email = $this->request->post('email');
//   $password = $this->request->post('password');
//   $result = $this->authenticator->login($email, $password);

//   if ($result instanceof User) {
//     $this->sessionService->set(
//       'user',
//       [
//         'id' => $result->getId(),
//         'role' => $result->getRole(),
//         'username' => $result->getName(),
//       ]
//     );

//     Redirector::redirectTo('/');
//   } else {
//     $this->renderLoginView("incorrect email or password");
//   }
// }

// public function logout(): void
// {
//   $this->sessionService->end();
//   Redirector::redirectTo('/');
// }
