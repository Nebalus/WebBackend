<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\Module\Blog\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class DeleteBlogResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("Blog Deleted", StatusCodeInterface::STATUS_OK);
    }
}
