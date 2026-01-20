<?php

namespace Nebalus\Webapi\Api\Module\Blog\GetPublic;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;

class GetPublicBlogValidator extends AbstractValidator
{
    private int $page;
    private int $perPage;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::QUERY_PARAMS => S::object([
                "page" => S::number()->integer()->positive()->optional()->default(1),
                "per_page" => S::number()->integer()->positive()->optional()->default(10),
            ])
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->page = (int) ($queryParamsData["page"] ?? 1);
        $this->perPage = (int) ($queryParamsData["per_page"] ?? 10);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
