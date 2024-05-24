<?php
namespace App\Services\Authenticate;

use App\Services\Authenticate\IAuthResult;

class DBAuthResult implements IAuthResult
{
    private ?IIdentity $identity;
    private array $messages;
    private bool $isValid;

    public function __construct(IIdentity $identity = null, array $messages = [], bool $isValid = false)
    {
        $this->identity = $identity;
        $this->messages = $messages;
        $this->isValid = $isValid;
    }

    public function getIdentity(): ?IIdentity
    {
        return $this->identity;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}