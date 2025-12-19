<?php

namespace UnitTesting\Api\Health;

use Nebalus\Webapi\Api\Health\HealthAction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Psr7\Factory\StreamFactory;

class HealthActionTest extends TestCase
{
    #[Test]
    public function testInvokeReturnsHealthyResponse(): void
    {
        $action = new HealthAction();
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = [];

        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStream();

        $response->method('getBody')->willReturn($body);

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'text/html')
            ->willReturnSelf();

        $result = $action->__invoke($request, $response, $pathArgs);

        $body->rewind();
        $this->assertEquals("This service is healthy", $body->getContents());
        $this->assertSame($response, $result);
    }
}
