<?php

namespace UnitTesting\Api\Module\Referral\Analytics\Click;

use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralService;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class ClickReferralActionTest extends TestCase
{
    private ClickReferralService $service;
    private ClickReferralValidator $validator;
    private ClickReferralAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ClickReferralService::class);
        $this->validator = $this->createMock(ClickReferralValidator::class);
        $this->action = new ClickReferralAction($this->validator, $this->service);
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
