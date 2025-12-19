<?php

namespace UnitTesting\Api\Module\Linktree\Click;

use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class ClickLinktreeValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePasses(): void
    {
        $validator = new ClickLinktreeValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [];

        $validator->validate($request, $pathArgs);

        $this->assertTrue(true); // Should not throw exception
    }
}
