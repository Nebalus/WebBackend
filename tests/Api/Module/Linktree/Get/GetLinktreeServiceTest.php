<?php

namespace UnitTesting\Api\Module\Linktree\Get;

use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeResponder;
use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetLinktreeServiceTest extends TestCase
{
    private GetLinktreeResponder $responder;
    private GetLinktreeService $service;
    private GetLinktreeValidator $validator;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(GetLinktreeResponder::class);
        $this->service = new GetLinktreeService($this->responder);
        $this->validator = $this->createMock(GetLinktreeValidator::class);
    }

    #[Test]
    public function testExecuteReturnsResultFromResponder(): void
    {
        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator);

        $this->assertSame($expectedResult, $result);
    }
}
