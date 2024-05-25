<?php

namespace App\Middleware\QuestValidation;

use App\Middleware\InputValidation\InputValidationMiddleware;
use App\Validator\IValidationChain;
use App\Validator\MinMaxLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;
use App\Middleware\QuestValidation\QuestionsRule;

class QuestValidationMiddleware extends InputValidationMiddleware
{
  public function __construct(IValidationChain $validationChain = null)
  {
    $validationChain = $validationChain ?: new ValidationChain();
    $validationChain->addRules('quizTitle', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 100)]);
    $validationChain->addRules('quizDescription', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 200)]);
    $validationChain->addRules('requiredWallet', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 20)]);
    $validationChain->addRules('timeRequired', [new RequiredValidationRule()]);
    $validationChain->addRules('expiryDate', [new RequiredValidationRule()]);
    $validationChain->addRules('participantsLimit', [new RequiredValidationRule()]);
    $validationChain->addRules('poolAmount', [new RequiredValidationRule()]);
    $validationChain->addRules('token', [new RequiredValidationRule(), new MinMaxLengthValidationRule(2, 4)]);
    $validationChain->addRules('questions', [new QuestionsRule()]);
    parent::__construct($validationChain);
  }
}