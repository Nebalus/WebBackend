<?php

namespace UnitTesting\Api\Admin\User\Role\GetAll;

use Nebalus\Webapi\Api\Admin\User\Role\GetAll\GetAllRoleFromUserValidator;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetAllRoleFromUserValidatorTest extends TestCase
{
    private GetAllRoleFromUserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new GetAllRoleFromUserValidator();
    }

    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['user_id' => 1];

        $this->validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $this->validator->getUserId());
        $this->assertEquals(1, $this->validator->getUserId()->asInt());
    }

    #[Test]
    public function testValidateThrowsExceptionWithInvalidData(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->method('getParsedBody')->willReturn([]);
        $request->method('getQueryParams')->willReturn([]);

        $pathArgs = ['user_id' => 'invalid'];

        $this->expectException(ApiInvalidArgumentException::class);

        $this->validator->validate($request, $pathArgs);
    }
}
