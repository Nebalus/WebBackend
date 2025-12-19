<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Click;

use Nebalus\Sanitizr\SanitizrStatic as S;

use Nebalus\Webapi\Api\AbstractValidator;

class ClickLinktreeValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct(S::object([]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        // TODO: Implement onValidate() method.
    }
}
