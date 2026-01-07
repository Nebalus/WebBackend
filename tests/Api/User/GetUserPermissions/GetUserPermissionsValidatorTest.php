<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\GetUserPermissions;

use Nebalus\Webapi\Api\User\GetUserPermissions\GetUserPermissionsValidator;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetUserPermissionsValidatorTest extends TestCase
{
    private GetUserPermissionsValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new GetUserPermissionsValidator();
    }

    public function testValidate(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = ['user_id' => '123'];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $this->validator->getUserId());
        $this->assertEquals(123, $this->validator->getUserId()->asInt());
    }
}
