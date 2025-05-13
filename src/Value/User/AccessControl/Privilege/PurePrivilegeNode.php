<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Privilege;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Value\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class PurePrivilegeNode
{
    use SanitizrValueObjectTrait;

    public const int MAX_LENGTH = 128;
    private const string REGEX = '/^(([a-z_])+(\.[a-z_.*]+)*)$/';

    private function __construct(
        private readonly string $node
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return Sanitizr::string()->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function fromString(string $node): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($node);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid pure privilege node: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function isParentOf(PurePrivilegeNode $toCheckNode): bool
    {
        return str_starts_with($this->node, $toCheckNode->node);
    }

    public function asDestructured(): array
    {
        $finalArray = [];

        $ref = &$finalArray;
        foreach (explode('.', $this->node) as $key) {
            $ref = &$ref[$key];
        }
        return $finalArray; // DO NOT REMOVE // THIS IS THE FINAL RETURN VALUE
    }

    public function asString(): string
    {
        return $this->node;
    }
}
