<?php

namespace UnitTesting\Api\Module\Linktree\Delete;

use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeResponder;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteLinktreeServiceTest extends TestCase
{
    private DeleteLinktreeResponder $responder;
    private DeleteLinktreeService $service;
    private DeleteLinktreeValidator $validator;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(DeleteLinktreeResponder::class);
        $this->service = new DeleteLinktreeService($this->responder);
        $this->validator = $this->createMock(DeleteLinktreeValidator::class);
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
