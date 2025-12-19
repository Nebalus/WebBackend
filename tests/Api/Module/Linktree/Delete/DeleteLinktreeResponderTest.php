<?php

namespace UnitTesting\Api\Module\Linktree\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Linktree\Delete\DeleteLinktreeResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteLinktreeResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new DeleteLinktreeResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('PLACEHOLDER', $result->getMessage());
        $this->assertEmpty($result->getPayload()['payload']);
    }
}
