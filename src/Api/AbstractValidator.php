<?php

namespace Nebalus\Webapi\Api;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Exception\ApiValidationException;
use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;
use Nebalus\Webapi\Utils\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Webapi\Value\Internal\Validation\ValidatedData;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractValidator
{
    public const int MAX_RECURSION = 100;

    protected function __construct(private readonly array $rules = [])
    {
    }

    /**
     * @throws ApiException
     */
    public function validate(ServerRequestInterface $request, array $rawPathArgs = []): void
    {
        $schema = Sanitizr::object($this->rules);
        $validData = $schema->safeParse([
            'body' => json_decode($request->getBody()->getContents(), true, self::MAX_RECURSION) ?? [],
            'query_param' => $request->getQueryParams() ?? [],
            'path_args' => $rawPathArgs,
        ]);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException($validData->getErrorMessage());
        }

        $validatedData = ValidatedData::from($validData->getValue()["body"] ?? [], $validData->getValue()["query_param"] ?? [], $validData->getValue()["path_args"] ?? []);
        $this->onValidate($validatedData);
    }

    /**
     * @throws ApiException
     */
    abstract protected function onValidate(ValidatedData $validatedData): void;
}
