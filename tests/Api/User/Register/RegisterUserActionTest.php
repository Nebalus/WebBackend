<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Register;

use Nebalus\Webapi\Api\User\Register\RegisterUserAction;
use Nebalus\Webapi\Api\User\Register\RegisterUserService;
use Nebalus\Webapi\Api\User\Register\RegisterUserValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class RegisterUserActionTest extends TestCase
{
    private RegisterUserAction $action;
    private RegisterUserValidator&MockObject $validator;
    private RegisterUserService&MockObject $service;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(RegisterUserValidator::class);
        $this->service = $this->createMock(RegisterUserService::class);
        $this->action = new RegisterUserAction($this->validator, $this->service);
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
            ->willReturn(['success' => true]);

        $result->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(201);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['success' => true], 201)
            ->willReturn($response);

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
