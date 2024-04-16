<?php

require_once 'AppController.php';

class ErrorController extends AppController
{

  public function notFound($message = '')
  {
    http_response_code(404);
    $this->render('error', ['code' => 404, 'message' => $message]);
  }

  public function serverError($message = '')
  {
    http_response_code(500);
    $this->render('error', ['code' => 500, 'message' => $message]);
  }
}