<?php

namespace App\Services\Authenticate;

interface IAuthResult {
    public function getIdentity();
    public function getMessages();   
    public function isValid();
}