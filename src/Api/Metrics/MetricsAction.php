<?php

namespace Nebalus\Webapi\Api\Metrics;

use Nebalus\Webapi\Api\AbstractAction;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use Throwable;

class MetricsAction extends AbstractAction
{
    public function __construct(
        private readonly RenderTextFormat $renderTextFormat,
        private readonly CollectorRegistry $registry,
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $result = $this->renderTextFormat->render($this->registry->getMetricFamilySamples());
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
