<?php

namespace App\Services\Register;

interface IRegisterResult {
  public function getMessages();   
  public function isValid();
}