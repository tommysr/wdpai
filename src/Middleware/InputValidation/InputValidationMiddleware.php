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

  protected array $toValidate = [];

  public function process(IFullRequest $request, IHandler $handler): IResponse
  { 
    try {
      $errors = $this->validationChain->validateFields($this->toValidate);
 
      if (count($errors) > 0) {
        return new JsonResponse(['errors' => $errors]);
      }
    } catch (\Exception $e) {
      return new JsonResponse(['errors' => [$e->getMessage()]]);
    }

    return $this->next ? $this->next->process($request, $handler) : $handler->handle($request);
  }
}