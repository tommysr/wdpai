<?php
namespace App\Controllers;

use App\Controllers\Interfaces\IErrorController;
use App\Request\IRequest;
use App\Middleware\IResponse;

class ErrorController extends AppController implements IErrorController
{
  public function index(IRequest $request): IResponse
  {
    return $this->render('error', ['code' => 404, 'message' => '']);
  }

  public function error(IRequest $request, int $code): IResponse
  {
    return $this->render('error', ['code' => $code, 'message' => '']);
  }
}