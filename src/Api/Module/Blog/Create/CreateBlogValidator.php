<?php

namespace Nebalus\Webapi\Api\Module\Blog\Create;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Config\Types\RequestParamTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;

class CreateBlogValidator extends AbstractValidator
{
    private UserId $userId;

    private ReferralLabel $label;
    private Url $url;
    private bool $disabled;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                "user_id" => UserId::getSchema(),
            ]),
            RequestParamTypes::BODY => S::object([
                'label' => ReferralLabel::getSchema(),
                'url' => Url::getSchema(),
                'disabled' => S::boolean()->optional()->default(false),
            ]),
        ]));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ApiException
     */
    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->userId = UserId::from($pathArgsData['user_id']);

        $this->label = ReferralLabel::from($bodyData['label']);
        $this->url = Url::from($bodyData['url']);
        $this->disabled = $bodyData['disabled'];
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getLabel(): ReferralLabel
    {
        return $this->label;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
