<?php
namespace App\Controllers;

use App\Controllers\IRootController;
use App\Request\IRequest;

class ErrorController extends AppController implements IRootController
{
  public function index(IRequest $request)
  {
    $this->render('error', ['message' => 'unknown', 'code' => 500]);
  }

  public function error(IRequest $request, int $code)
  {
    $this->render('error', ['code' => $code, 'message' => '']);
  }

  private function default_error($code, $message)
  {
    $this->render('error', ['title' => 'Error', 'code' => $code, 'message' => $message]);
  }

  public function notFound($message = '')
  {
    http_response_code(404);
    $this->default_error(404, $message);
  }

  public function serverError($message = '')
  {
    http_response_code(500);
    $this->default_error(500, $message);
  }
}