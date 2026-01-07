<?php

namespace UnitTesting\Api\Module\Linktree\Edit;

use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeResponder;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeService;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeValidator;
use Nebalus\Webapi\Slim\ResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EditLinktreeServiceTest extends TestCase
{
    private EditLinktreeResponder $responder;
    private EditLinktreeService $service;
    private EditLinktreeValidator $validator;

    protected function setUp(): void
    {
        $this->responder = $this->createMock(EditLinktreeResponder::class);
        $this->service = new EditLinktreeService($this->responder);
        $this->validator = $this->createMock(EditLinktreeValidator::class);
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
