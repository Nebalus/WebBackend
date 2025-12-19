<?php

namespace UnitTesting\Api\Module\Linktree\Delete;

use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class DeleteLinktreeValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePasses(): void
    {
        $validator = new DeleteLinktreeValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [];

        $validator->validate($request, $pathArgs);

        $this->assertTrue(true); // Should not throw exception
    }
}
