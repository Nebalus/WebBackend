<?php

namespace UnitTesting\Api\Module\Linktree\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Linktree\Edit\EditLinktreeResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EditLinktreeResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new EditLinktreeResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('PLACEHOLDER', $result->getMessage());
        $this->assertEmpty($result->getPayload()['payload']);
    }
}
