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
        private RenderTextFormat $renderTextFormat,
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $registry = CollectorRegistry::getDefault();
        $result = $this->renderTextFormat->render($registry->getMetricFamilySamples());
        $response->getBody()->write($result);

        return $response;
    }
}
