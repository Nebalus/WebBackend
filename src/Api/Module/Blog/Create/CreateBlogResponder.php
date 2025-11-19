<?php

namespace Nebalus\Webapi\Api\Module\Blog\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Result\Result;

class CreateBlogResponder
{
    public function render(Referral $referral): ResultInterface
    {
        $fields = [

        ];

        return Result::createSuccess("Blog Created", StatusCodeInterface::STATUS_CREATED, $fields);
    }
}
