<?php

namespace UnitTesting\Api\Module\Linktree\Create;

use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class CreateLinktreeValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePasses(): void
    {
        $validator = new CreateLinktreeValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [];

        $validator->validate($request, $pathArgs);

        $this->assertTrue(true); // Should not throw exception
    }
}
