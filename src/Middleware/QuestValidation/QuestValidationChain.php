<?php

namespace App\Middleware\QuestValidation;

use App\Validator\ImageValidationRule;
use App\Validator\MinMaxFloatValidationRule;
use App\Validator\MinMaxIntValidationRule;
use App\Validator\MinMaxLengthValidationRule;
use App\Validator\MinMaxValidationRule;
use App\Validator\RequiredValidationRule;
use App\Validator\ValidationChain;
use App\Middleware\QuestValidation\QuestionsRule;


class QuestValidationChain extends ValidationChain
{
  public function __construct()
  {
    $requiredValidationRule = new RequiredValidationRule();
    $this->strict = true;

    $this->addRules('questThumbnail', [$requiredValidationRule, new ImageValidationRule()]);
    $this->addRules('title', [$requiredValidationRule, new MinMaxLengthValidationRule(5, 90)]);
    $this->addRules('description', [$requiredValidationRule, new MinMaxLengthValidationRule(20, 300)]);
    $this->addRules('blockchain', [$requiredValidationRule, new MinMaxLengthValidationRule(3, 50)]);
    $this->addRules('token', [$requiredValidationRule, new MinMaxLengthValidationRule(3, 20)]);
    $this->addRules('expiryDate', [$requiredValidationRule]); 
    $this->addRules('payoutDate', [$requiredValidationRule]); 
    $this->addRules('minutesRequired', [$requiredValidationRule,  new MinMaxIntValidationRule(1, 120)]);
    $this->addRules('participantsLimit', [$requiredValidationRule, new MinMaxIntValidationRule(20, 1000)]);
    $this->addRules('poolAmount', [$requiredValidationRule, new MinMaxFloatValidationRule(0.0, PHP_FLOAT_MAX)]);
    $this->addRules('questions', [$requiredValidationRule, new QuestionsRule()]);
  }
}