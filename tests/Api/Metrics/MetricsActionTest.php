<?php

namespace UnitTesting\Api\Metrics;

use Nebalus\Webapi\Api\Metrics\MetricsAction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Psr7\Factory\StreamFactory;

class MetricsActionTest extends TestCase
{
    #[Test]
    public function testInvokeReturnsMetrics(): void
    {
        $renderTextFormat = $this->createMock(RenderTextFormat::class);
        $registry = $this->createMock(CollectorRegistry::class);
        $action = new MetricsAction($renderTextFormat, $registry);

        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = [];

        $registry->expects($this->once())
            ->method('getMetricFamilySamples')
            ->willReturn([]);

        $renderTextFormat->expects($this->once())
            ->method('render')
            ->with([])
            ->willReturn('metrics_data');

        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStream();

        $response->method('getBody')->willReturn($body);

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', RenderTextFormat::MIME_TYPE)
            ->willReturnSelf();

        $result = $action->__invoke($request, $response, $pathArgs);

        $body->rewind();
        $this->assertEquals("metrics_data", $body->getContents());
        $this->assertSame($response, $result);
    }
}
