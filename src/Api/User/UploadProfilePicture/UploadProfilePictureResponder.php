<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\UploadProfilePicture;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class UploadProfilePictureResponder
{
    public function render(string $filename): ResultInterface
    {
        $fields = [
            'filename' => $filename,
        ];

        return Result::createSuccess('Profile picture uploaded successfully', StatusCodeInterface::STATUS_OK, $fields);
    }
}
