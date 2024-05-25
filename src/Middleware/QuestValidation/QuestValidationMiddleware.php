<?php

namespace App\Middleware\QuestValidation;

use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Validator\IValidationChain;

class QuestValidationMiddleware extends InputValidationMiddleware
{
  public function __construct(IValidationChain $validationChain)
  {
    $this->validationChain = $validationChain;
  }
}