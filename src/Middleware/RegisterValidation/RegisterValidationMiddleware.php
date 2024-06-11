<?php

namespace App\Middleware\RegisterValidation;

use App\Middleware\IHandler;
use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Middleware\IResponse;
use App\Request\IFullRequest;
use App\Validator\Factory\IValidationChainFactory;

class RegisterValidationMiddleware extends InputValidationMiddleware
{
    private IValidationChainFactory $validationFactory;

    public function __construct(IValidationChainFactory $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }

    public function process(IFullRequest $request, IHandler $handler): IResponse
    {
        $registration_method = $request->getParsedBody()['registration_method'] ?? 'db';
        $validationChain = $this->validationFactory->createValidationChain($registration_method);
        $this->validationChain = $validationChain;

        return parent::process($request, $handler);
    }
}