<?php

namespace UnitTesting\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class EditLinktreeActionTest extends TestCase
{
    private EditLinktreeService $service;
    private EditLinktreeValidator $validator;
    private EditLinktreeAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(EditLinktreeService::class);
        $this->validator = $this->createMock(EditLinktreeValidator::class);
        $this->action = new EditLinktreeAction($this->service, $this->validator);
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
