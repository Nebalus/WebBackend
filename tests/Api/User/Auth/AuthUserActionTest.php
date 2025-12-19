<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Auth;

use Nebalus\Webapi\Api\User\Auth\AuthUserAction;
use Nebalus\Webapi\Api\User\Auth\AuthUserService;
use Nebalus\Webapi\Api\User\Auth\AuthUserValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class AuthUserActionTest extends TestCase
{
    private AuthUserAction $action;
    private AuthUserValidator&MockObject $validator;
    private AuthUserService&MockObject $service;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(AuthUserValidator::class);
        $this->service = $this->createMock(AuthUserService::class);
        $this->action = new AuthUserAction($this->validator, $this->service);
    }

    public function testAction(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $result = $this->createMock(ResultInterface::class);
        $pathArgs = [];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator)
            ->willReturn($result);

        $result->expects($this->once())
            ->method('getPayload')
            ->willReturn(['token' => 'jwt_token']);

        $result->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['token' => 'jwt_token'], 200)
            ->willReturn($response);

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
