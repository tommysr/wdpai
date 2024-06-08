<?php
namespace App\Controllers;

use App\Controllers\Interfaces\IErrorController;
use App\Request\IFullRequest;
use App\Middleware\IResponse;

class ErrorController extends AppController implements IErrorController
{
  public function getIndex(IFullRequest $request): IResponse
  {
    return $this->render('error', ['code' => 404, 'message' => 'not found']);
  }

  public function getError(IFullRequest $request, int $code, array $messages = []): IResponse
  {
    $query_messages = $request->getQuery('messages', []);
    return $this->render('error', ['code' => $code, 'messages' => array_merge($messages, $query_messages)]);
  }
}