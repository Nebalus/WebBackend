<?php

namespace Nebalus\Webapi\Api;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Sanitizr\Schema\SanitizrObject;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    protected function __construct(
        private readonly SanitizrObject $validationSchema
    ) {
    }

    /**
     * @throws ApiException
     */
    public function validate(ServerRequestInterface $request, array $pathArgs = []): void
    {
        $validData = $this->validationSchema->safeParse([
            RequestParamTypes::BODY => $request->getParsedBody() ?? [],
            RequestParamTypes::QUERY_PARAMS => $request->getQueryParams() ?? [],
            RequestParamTypes::PATH_ARGS => $pathArgs,
        ]);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException($validData->getErrorMessage(), StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
        }

        $this->onValidate($validData->getValue()[RequestParamTypes::BODY] ?? [], $validData->getValue()[RequestParamTypes::QUERY_PARAMS] ?? [], $validData->getValue()[RequestParamTypes::PATH_ARGS] ?? []);
    }

    /**
     * @throws ApiException
     */
    abstract protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void;
}
