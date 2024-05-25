<?php

namespace App\Middleware\InputValidation;

use App\Middleware\BaseMiddleware;
use App\Validator\IValidationChain;
use App\Request\IFullRequest;
use App\Middleware\IHandler;
use App\Middleware\IResponse;
use App\Middleware\JsonResponse;

abstract class InputValidationMiddleware extends BaseMiddleware
{
  protected IValidationChain $validationChain;

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    try {
      $errors = $this->validationChain->validateFields($request->getParsedBody());

      if (count($errors) > 0) {
        return new JsonResponse($errors);
      }
    } catch (\Exception $e) {
      return new JsonResponse(['error' => $e->getMessage()]);
    }

    if ($this->next !== null) {
      return $this->next->process($request, $handler);
    }

    return $handler->handle($request);
  }
}