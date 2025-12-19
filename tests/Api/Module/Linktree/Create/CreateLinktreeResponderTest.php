<?php

namespace UnitTesting\Api\Module\Linktree\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Linktree\Create\CreateLinktreeResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateLinktreeResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new CreateLinktreeResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('PLACEHOLDER', $result->getMessage());
        $this->assertEmpty($result->getPayload()['payload']);
    }
}
