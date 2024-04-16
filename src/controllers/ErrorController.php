<?php

require_once 'AppController.php';

class ErrorController extends AppController
{
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