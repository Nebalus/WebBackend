<?php

namespace Nebalus\Webapi\Slim\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Result\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

readonly class DefaultErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private App $app,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        $errorMessage = 'Internal server error... please contact an admin!';

        switch ($exception::class) {
            case HttpMethodNotAllowedException::class:
                $statusCode = StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED;
                $errorMessage = 'Method not allowed';
                break;
            case HttpNotFoundException::class:
                $notFoundResult = Result::createError("This is the API for my website https://www.nebalus.dev", StatusCodeInterface::STATUS_NOT_FOUND);
                $response = $this->app->getResponseFactory()->createResponse();
                $response->getBody()->write($notFoundResult->getPayloadAsJson());
                return $response->withHeader('Content-Type', 'application/json')->withStatus($notFoundResult->getStatusCode());
        }

        if ($exception instanceof ApiException) {
            $statusCode = $exception->getCode();
            $errorMessage = $exception->getMessage();
        }

        $result = null;

        if ($displayErrorDetails) {
            $throwableAsArray = [];
            $throwableAsArray['error'] = $exception->getMessage();
            $throwableAsArray['class'] = get_class($exception);
            $throwableAsArray['code'] = $exception->getCode();
            $throwableAsArray['file'] = $exception->getFile();
            $throwableAsArray['line'] = $exception->getLine();
            $throwableAsArray['trace'] = $exception->getTrace();

            $result = Result::createError($errorMessage, $statusCode, $throwableAsArray);
        }

        if (is_null($result)) {
            $result = Result::createError($errorMessage, $statusCode);
        }

        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write($result->getPayloadAsJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus($result->getStatusCode());
    }
}
