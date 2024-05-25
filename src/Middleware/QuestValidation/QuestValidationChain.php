<?php

namespace App\Middleware\QuestValidation;

use App\Validator\MinMaxLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;
use App\Middleware\QuestValidation\QuestionsRule;


class QuestValidationChain extends ValidationChain
{
  public function __construct() {
    $this->addRules('quizTitle', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 100)]);
    $this->addRules('quizDescription', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 200)]);
    $this->addRules('requiredWallet', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 20)]);
    $this->addRules('timeRequired', [new RequiredValidationRule()]);
    $this->addRules('expiryDate', [new RequiredValidationRule()]);
    $this->addRules('participantsLimit', [new RequiredValidationRule()]);
    $this->addRules('poolAmount', [new RequiredValidationRule()]);
    $this->addRules('token', [new RequiredValidationRule(), new MinMaxLengthValidationRule(2, 4)]);
    $this->addRules('questions', [new QuestionsRule()]);
  }
}