<?php

namespace UnitTesting\Api\Admin\Role\Permission\GetAll;

use Nebalus\Webapi\Api\Admin\Role\Permission\GetAll\GetAllRolePermissionValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetAllRolePermissionValidatorTest extends TestCase
{
    private GetAllRolePermissionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new GetAllRolePermissionValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(RoleId::class, $this->validator->getRoleId());
        $this->assertEquals(1, $this->validator->getRoleId()->asInt());
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 'invalid'];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
