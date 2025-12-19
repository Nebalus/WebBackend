<?php

namespace UnitTesting\Api\Module\Linktree\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Linktree\Click\ClickLinktreeResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickLinktreeResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new ClickLinktreeResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Linktree found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals('Test', $data['description']);
        $this->assertCount(3, $data['entrys']);
        $this->assertEquals('Test 1', $data['entrys'][0]['label']);
    }
}
