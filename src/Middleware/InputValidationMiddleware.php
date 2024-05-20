<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;
use App\Validator\IValidationChain;
use App\Request\IFullRequest;
use App\Middleware\IHandler;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;

class InputValidationMiddleware extends BaseMiddleware
{
  private IValidationChain $validationChain;

  public function __construct(IValidationChain $validationChain)
  {
    $this->validationChain = $validationChain;
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $errors = $this->validationChain->validateFields($request->getParsedBody());

    if (count($errors) > 0) {
      return new JsonResponse($errors, 400);
    }

    if ($this->next !== null) {
      return $this->next->process($request, $handler);
    }

    return $handler->handle($request);
  }
}