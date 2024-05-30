<?php

namespace App\Middleware\QuestValidation;

use App\Validator\MinMaxLengthValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;
use App\Middleware\QuestValidation\QuestionsRule;


class QuestValidationChain extends ValidationChain
{
  public function __construct()
  {
    $this->strict = true;
    $this->addRules('title', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 100)]);
    $this->addRules('description', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 200)]);
    $this->addRules('blockchain', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 20)]);
    $this->addRules('minutesRequired', [new RequiredValidationRule()]);
    $this->addRules('expiryDate', [new RequiredValidationRule()]);
    $this->addRules('payoutDate', [new RequiredValidationRule()]);
    $this->addRules('participantsLimit', [new RequiredValidationRule()]);
    $this->addRules('poolAmount', [new RequiredValidationRule()]);
    $this->addRules('token', [new RequiredValidationRule(), new MinMaxLengthValidationRule(2, 4)]);
    $this->addRules('questions', [new QuestionsRule()]);
  }
}