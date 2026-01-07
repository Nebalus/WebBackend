<?php

namespace UnitTesting\Api\Admin\Role\Permission\Upsert;

use Nebalus\Webapi\Api\Admin\Role\Permission\Upsert\UpsertRolePermissionValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class UpsertRolePermissionValidatorTest extends TestCase
{
    private UpsertRolePermissionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UpsertRolePermissionValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = [
            [
                'node' => 'test.node.one',
                'allow_all_sub_permissions' => true,
                'value' => null
            ]
        ];
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(RoleId::class, $this->validator->getRoleId());
        $this->assertEquals(1, $this->validator->getRoleId()->asInt());

        $this->assertInstanceOf(PermissionRoleLinkCollection::class, $this->validator->getPermissionRoleLinks());
        $this->assertGreaterThan(0, iterator_count($this->validator->getPermissionRoleLinks()));
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = [
            [
                'node' => 'invalid node', // Invalid format
                'allow_all_sub_permissions' => true
            ]
        ];
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
