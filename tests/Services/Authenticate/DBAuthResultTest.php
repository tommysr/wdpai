<?php
use PHPUnit\Framework\TestCase;
use App\Services\Authenticate\DBAuthResult;
use App\Services\Authenticate\IIdentity;

class DBAuthResultTest extends TestCase
{
    public function testGetIdentity()
    {
        $identity = $this->createMock(IIdentity::class);
        $messages = ['Invalid credentials'];
        $isValid = false;

        $authResult = new DBAuthResult($identity, $messages, $isValid);

        $this->assertSame($identity, $authResult->getIdentity());
    }

    public function testGetMessages()
    {
        $identity = $this->createMock(IIdentity::class);
        $messages = ['Invalid credentials'];
        $isValid = false;

        $authResult = new DBAuthResult($identity, $messages, $isValid);

        $this->assertSame($messages, $authResult->getMessages());
    }

    public function testIsValid()
    {
        $identity = $this->createMock(IIdentity::class);
        $messages = ['Invalid credentials'];
        $isValid = false;

        $authResult = new DBAuthResult($identity, $messages, $isValid);

        $this->assertFalse($authResult->isValid());
    }
}