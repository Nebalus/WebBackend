<?php

namespace UnitTesting\Api\Module\Linktree\Click;

use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeAction;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class ClickLinktreeActionTest extends TestCase
{
    private ClickLinktreeService $service;
    private ClickLinktreeValidator $validator;
    private ClickLinktreeAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ClickLinktreeService::class);
        $this->validator = $this->createMock(ClickLinktreeValidator::class);
        $this->action = new ClickLinktreeAction($this->service, $this->validator);
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
