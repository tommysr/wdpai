<?php

namespace App\Middleware\QuestValidation;

use App\Validator\MinMaxLengthValidationRule;
use App\Validator\MinMaxValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;
use App\Middleware\QuestValidation\QuestionsRule;


class QuestValidationChain extends ValidationChain
{
  public function __construct()
  { 
    // TODO: add some rules
    $this->strict = true;
    $this->addRules('questThumbnail', [new RequiredValidationRule()]);
    $this->addRules('title', [new RequiredValidationRule(), new MinMaxLengthValidationRule(5, 90)]);
    $this->addRules('description', [new RequiredValidationRule(), new MinMaxLengthValidationRule(20, 300)]);
    $this->addRules('blockchain', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 50)]);
    $this->addRules('minutesRequired', [new RequiredValidationRule(), new MinMaxValidationRule(1, 120)]);
    $this->addRules('expiryDate', [new RequiredValidationRule()]); // TODO:
    $this->addRules('payoutDate', [new RequiredValidationRule()]); // TODO:
    $this->addRules('participantsLimit', [new RequiredValidationRule(), new MinMaxValidationRule(20, 1000)]);
    $this->addRules('poolAmount', [new RequiredValidationRule(), new MinMaxValidationRule(0, PHP_INT_MAX)]); // TODO: do it for float
    $this->addRules('token', [new RequiredValidationRule(), new MinMaxLengthValidationRule(3, 20)]);
    $this->addRules('questions', [new QuestionsRule()]);
  }
}