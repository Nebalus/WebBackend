<?php

namespace UnitTesting\Api\Module\Linktree\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Linktree\Get\GetLinktreeResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetLinktreeResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new GetLinktreeResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('PLACEHOLDER', $result->getMessage());
        $this->assertEmpty($result->getPayload()['payload']);
    }
}
