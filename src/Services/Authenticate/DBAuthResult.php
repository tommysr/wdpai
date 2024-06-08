<?php
namespace App\Services\Authenticate;

use App\Result\Result;
use App\Services\Authenticate\IAuthResult;

class DBAuthResult extends Result implements IAuthResult
{
    private ?IIdentity $identity;
    private array $messages;
    private bool $isValid;

    public function __construct(IIdentity $identity = null, array $messages = [], bool $isValid = false)
    {
        parent::__construct($messages, $isValid);
        $this->identity = $identity;
    }

    public function getIdentity(): ?IIdentity
    {
        return $this->identity;
    }
}