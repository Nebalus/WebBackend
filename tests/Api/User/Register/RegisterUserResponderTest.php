<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Register;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\User\Register\RegisterUserResponder;
use PHPUnit\Framework\TestCase;

class RegisterUserResponderTest extends TestCase
{
    private RegisterUserResponder $responder;

    protected function setUp(): void
    {
        $this->responder = new RegisterUserResponder();
    }

    public function testRender(): void
    {
        $result = $this->responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $result->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'User registered',
            'status_code' => 201,
            'payload' => []
        ], $result->getPayload());
    }
}
