<?php

namespace Nebalus\Ownsite\Controller\Referral;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReferralApiDeleteController
{
    public function __construct()
    {
    }

    public function referral(Request $request, Response $response): Response
    {
        return $response;
    }
}
