<?php

namespace UnitTesting\Api\Module\Linktree\Click;

use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeResponder;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickLinktreeServiceTest extends TestCase
{
    private ClickLinktreeResponder $responder;
    private ClickLinktreeService $service;
    private ClickLinktreeValidator $validator;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(ClickLinktreeResponder::class);
        $this->service = new ClickLinktreeService($this->responder);
        $this->validator = $this->createMock(ClickLinktreeValidator::class);
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
