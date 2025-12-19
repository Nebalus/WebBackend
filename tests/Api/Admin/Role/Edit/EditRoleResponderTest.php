<?php

namespace UnitTesting\Api\Admin\Role\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Edit\EditRoleResponder;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EditRoleResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $roleId = RoleId::from(1);
        $roleName = RoleName::from('admin');
        $roleDescription = RoleDescription::from('Administrator role');
        $roleColor = RoleHexColor::from('FF0000');
        $accessLevel = RoleAccessLevel::from(100);
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        $role = Role::from(
            $roleId,
            $roleName,
            $roleDescription,
            $roleColor,
            $accessLevel,
            true, // appliesToEveryone
            false, // deletable
            true, // editable
            false, // disabled
            $createdAt,
            $updatedAt
        );

        $responder = new EditRoleResponder();
        $result = $responder->render($role);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Role edited', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals(1, $data['role_id']);
        $this->assertEquals('admin', $data['name']);
        $this->assertEquals('Administrator role', $data['description']);
        $this->assertEquals('FF0000', $data['color']);
        $this->assertEquals(100, $data['access_level']);
        $this->assertTrue($data['applies_to_everyone']);
        $this->assertFalse($data['deletable']);
        $this->assertTrue($data['editable']);
        $this->assertFalse($data['disabled']);
        $this->assertEquals($createdAt->format(DATE_ATOM), $data['created_at']);
        $this->assertEquals($updatedAt->format(DATE_ATOM), $data['updated_at']);
    }
}
