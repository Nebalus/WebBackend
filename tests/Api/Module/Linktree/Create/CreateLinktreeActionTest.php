<?php

namespace UnitTesting\Api\Module\Linktree\Create;

use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class CreateLinktreeActionTest extends TestCase
{
    private CreateLinktreeService $service;
    private CreateLinktreeValidator $validator;
    private CreateLinktreeAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CreateLinktreeService::class);
        $this->validator = $this->createMock(CreateLinktreeValidator::class);
        $this->action = new CreateLinktreeAction($this->service, $this->validator);
    }

    #[Test]
    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = [];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(200);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 200)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
