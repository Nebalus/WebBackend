<?php

namespace UnitTesting\Api\Admin\Permission\Get;

use Nebalus\Webapi\Api\Admin\Permission\Get\GetPermissionValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetPermissionValidatorTest extends TestCase
{
    private GetPermissionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new GetPermissionValidator();
    }

    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['permission_id' => 123];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(PermissionId::class, $this->validator->getPermissionId());
        $this->assertEquals(123, $this->validator->getPermissionId()->asInt());
    }

    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['permission_id' => 'invalid'];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
