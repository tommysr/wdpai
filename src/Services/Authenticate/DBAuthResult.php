<?php
namespace App\Services\Authenticate;

use App\Services\Authenticate\IAuthResult;

class DBAuthResult implements IAuthResult
{
    private string $identity;
    private array $messages;
    private bool $isValid;

    public function __construct(string $identity, array $messages, bool $isValid = false)
    {
        $this->identity = $identity;
        $this->messages = $messages;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function isValid()
    {
        return $this->isValid;
    }
}