<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\UploadProfilePicture;

use Nebalus\Webapi\Api\AbstractAction;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;

class UploadProfilePictureAction extends AbstractAction
{
    public function __construct(
        private readonly UploadProfilePictureValidator $validator,
        private readonly UploadProfilePictureService $service,
    ) {
    }

    /**
     * @throws ApiException
     */
    protected function execute(Request $request, Response $response, array $pathArgs): Response
    {
        $this->validator->validate($request, $pathArgs);

        $requestingUser = $request->getAttribute(AttributeTypes::CLIENT_USER);
        $userPerms = $request->getAttribute(AttributeTypes::CLIENT_USER_PERMISSION_INDEX);
        $uploadedFiles = $request->getUploadedFiles();
        $result = $this->service->execute($this->validator, $requestingUser, $userPerms, $uploadedFiles);

        return $response->withJson($result->getPayload(), $result->getStatusCode());
    }
}
