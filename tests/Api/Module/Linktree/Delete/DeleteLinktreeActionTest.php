<?php

namespace UnitTesting\Api\Module\Linktree\Delete;

use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class DeleteLinktreeActionTest extends TestCase
{
    private DeleteLinktreeService $service;
    private DeleteLinktreeValidator $validator;
    private DeleteLinktreeAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(DeleteLinktreeService::class);
        $this->validator = $this->createMock(DeleteLinktreeValidator::class);
        $this->action = new DeleteLinktreeAction($this->service, $this->validator);
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
