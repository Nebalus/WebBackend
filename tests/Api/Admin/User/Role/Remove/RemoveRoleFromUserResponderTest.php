<?php

namespace UnitTesting\Api\Admin\User\Role\Remove;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\User\Role\Remove\RemoveRoleFromUserResponder;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RemoveRoleFromUserResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $role = Role::from(
            RoleId::from(1),
            RoleName::from('admin'),
            RoleDescription::from('Administrator role'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            true,
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $responder = new RemoveRoleFromUserResponder();
        $result = $responder->render($role);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Role Removed', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals(1, $data['role_id']);
        $this->assertEquals('admin', $data['name']);
        $this->assertEquals('Administrator role', $data['description']);
        $this->assertEquals('FF0000', $data['color']);
        $this->assertEquals(100, $data['access_level']);
        $this->assertTrue($data['applies_to_everyone']);
    }
}
