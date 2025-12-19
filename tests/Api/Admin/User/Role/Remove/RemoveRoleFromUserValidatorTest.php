<?php

namespace UnitTesting\Api\Admin\User\Role\Remove;

use Nebalus\Webapi\Api\Admin\User\Role\Remove\RemoveRoleFromUserValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class RemoveRoleFromUserValidatorTest extends TestCase
{
    private RemoveRoleFromUserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RemoveRoleFromUserValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['user_id' => 1, 'role_id' => 2];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $this->validator->getUserId());
        $this->assertEquals(1, $this->validator->getUserId()->asInt());

        $this->assertInstanceOf(RoleId::class, $this->validator->getRoleId());
        $this->assertEquals(2, $this->validator->getRoleId()->asInt());
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['user_id' => 'invalid', 'role_id' => 2];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
