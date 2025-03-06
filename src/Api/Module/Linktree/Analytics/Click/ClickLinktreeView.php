<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

class ClickLinktreeView
{
    public static function render(): ResultInterface
    {
        $fields = [
            "description" => "Test",
            "entrys" => [
                [
                    "position" => 1,
                    "label" => "Test 1",
                    "url" => "http://google.com",
                ],
                [
                    "position" => 2,
                    "label" => "Test 2",
                    "url" => "http://example.com",
                ],
                [
                    "position" => 3,
                    "label" => "Test 3",
                    "url" => "http://blank.com",
                ]
            ]
        ];

        return Result::createSuccess("Linktree found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
