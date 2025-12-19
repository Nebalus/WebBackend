<?php

namespace UnitTesting\Api\Module\Linktree\Create;

use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeResponder;
use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateLinktreeServiceTest extends TestCase
{
    private CreateLinktreeResponder $responder;
    private CreateLinktreeService $service;
    private CreateLinktreeValidator $validator;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(CreateLinktreeResponder::class);
        $this->service = new CreateLinktreeService($this->responder);
        $this->validator = $this->createMock(CreateLinktreeValidator::class);
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
