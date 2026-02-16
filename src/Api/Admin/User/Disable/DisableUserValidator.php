<?php

namespace Nebalus\Webapi\Api\Admin\User\Disable;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\UserId;

class DisableUserValidator extends AbstractValidator
{
    private UserId $userId;
    private bool $disabled;
    private ?string $reason;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                "disabled" => S::boolean(),
                "reason" => S::nullable(S::string()->min(1)->max(255)),
            ])
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->userId = UserId::from($pathArgsData["user_id"]);
        $this->disabled = $bodyData["disabled"];
        $this->reason = $bodyData["reason"] ?? null;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
