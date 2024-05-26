<?php
namespace App\Middleware\LoginValidation;

use App\Middleware\IHandler;
use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Middleware\IResponse;
use App\Request\IFullRequest;
use App\Validator\Factory\IValidationChainFactory;

class LoginValidationMiddleware extends InputValidationMiddleware
{
  private IValidationChainFactory $validationFactory;

  public function __construct(IValidationChainFactory $validationFactory)
  {
    $this->validationFactory = $validationFactory;
  }

  public function process(IFullRequest $request, IHandler $handler): IResponse
  {
    $login_method = $request->getParsedBody()['login_method'] ?? 'db';
    $this->toValidate = $request->getParsedBody();
    $validationChain = $this->validationFactory->createValidationChain($login_method);
    $this->validationChain = $validationChain;

    return parent::process($request, $handler);
  }
}
