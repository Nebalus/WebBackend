<?php

namespace UnitTesting\Api\Admin\Role\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\GetAll\GetAllRoleResponder;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllRoleResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $role1 = Role::from(
            RoleId::from(1),
            RoleName::from('admin'),
            RoleDescription::from('Admin role'),
            RoleHexColor::from('FF0000'),
            RoleAccessLevel::from(100),
            true,
            false,
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $role2 = Role::from(
            RoleId::from(2),
            RoleName::from('user'),
            RoleDescription::from('User role'),
            RoleHexColor::from('0000FF'),
            RoleAccessLevel::from(10),
            false,
            true,
            true,
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $roleCollection = RoleCollection::fromObjects($role1, $role2);

        $responder = new GetAllRoleResponder();
        $result = $responder->render($roleCollection);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('List of all roles', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['role_id']);
        $this->assertEquals('admin', $data[0]['name']);
        $this->assertEquals(2, $data[1]['role_id']);
        $this->assertEquals('user', $data[1]['name']);
    }
}
