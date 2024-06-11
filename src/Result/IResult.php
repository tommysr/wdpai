<?php

namespace App\Result;

interface IResult {
  public function getMessages(): array;   
  public function isValid(): bool;
}