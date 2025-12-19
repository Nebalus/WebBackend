<?php

namespace UnitTesting\Api\Admin\Role\Permission\Delete;

use Nebalus\Webapi\Api\Admin\Role\Permission\Delete\DeleteRolePermissionValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class DeleteRolePermissionValidatorTest extends TestCase
{
    private DeleteRolePermissionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DeleteRolePermissionValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = ['test.node.one', 'test.node.two'];
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(RoleId::class, $this->validator->getRoleId());
        $this->assertEquals(1, $this->validator->getRoleId()->asInt());

        $this->assertInstanceOf(PermissionNodeCollection::class, $this->validator->getPermissionNodes());
        $this->assertGreaterThan(0, iterator_count($this->validator->getPermissionNodes()));
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $body = ['invalid node']; // Invalid format
        $request->method('getParsedBody')->willReturn($body);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['role_id' => 1];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
