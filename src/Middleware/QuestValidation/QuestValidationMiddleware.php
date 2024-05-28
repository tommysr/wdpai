<?php

namespace App\Middleware\QuestValidation;

use App\Middleware\IHandler;
use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Middleware\IResponse;
use App\Request\IFullRequest;
use App\Validator\IValidationChain;

class QuestValidationMiddleware extends InputValidationMiddleware
{
  public function __construct(IValidationChain $validationChain)
  {
    $this->validationChain = $validationChain;
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $formData = $request->getBody();
    $parsedData = json_decode($formData, true) ?? [];
    $this->toValidate = $parsedData;
    return parent::process($request, $handler);
  }
}