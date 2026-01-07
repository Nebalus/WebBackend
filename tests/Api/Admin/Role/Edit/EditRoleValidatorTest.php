<?php

namespace UnitTesting\Api\Admin\Role\Edit;

use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class EditRoleValidatorTest extends TestCase
{
    private EditRoleValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EditRoleValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = [
            'name' => 'admin',
            'description' => 'Test Description',
            'color' => '0000FF',
            'access_level' => 10,
            'applies_to_everyone' => false,
            'disabled' => true
        ];
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(RoleId::class, $this->validator->getRoleId());
        $this->assertEquals(1, $this->validator->getRoleId()->asInt());

        $this->assertInstanceOf(RoleName::class, $this->validator->getRoleName());
        $this->assertEquals('admin', $this->validator->getRoleName()->asString());

        $this->assertInstanceOf(RoleDescription::class, $this->validator->getRoleDescription());
        $this->assertEquals('Test Description', $this->validator->getRoleDescription()->asString());

        $this->assertInstanceOf(RoleHexColor::class, $this->validator->getRoleColor());
        $this->assertEquals('0000FF', $this->validator->getRoleColor()->asString());

        $this->assertInstanceOf(RoleAccessLevel::class, $this->validator->getAccessLevel());
        $this->assertEquals(10, $this->validator->getAccessLevel()->asInt());

        $this->assertFalse($this->validator->appliesToEveryone());
        $this->assertTrue($this->validator->isDisabled());
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = [
            'name' => '', // Invalid name
            'description' => 'Test Description',
            'color' => 'invalid-color',
            'access_level' => 10,
            'applies_to_everyone' => false,
            'disabled' => true
        ];
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
