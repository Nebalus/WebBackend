<?php

namespace UnitTesting\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class EditLinktreeValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePasses(): void
    {
        $validator = new EditLinktreeValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [];

        $validator->validate($request, $pathArgs);

        $this->assertTrue(true); // Should not throw exception
    }
}
