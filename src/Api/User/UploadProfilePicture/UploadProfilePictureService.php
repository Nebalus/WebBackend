<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Api\User\UploadProfilePicture;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Psr\Http\Message\UploadedFileInterface;

readonly class UploadProfilePictureService
{
    private const array ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const int MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    private const string UPLOAD_DIR = __DIR__ . '/../../../../public/uploads/profile_images';

    public function __construct(
        private MySqlUserRepository $userRepository,
        private UploadProfilePictureResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(UploadProfilePictureValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms, array $uploadedFiles): ResultInterface
    {
        $isSelfUser = $validator->getUserId()->equals($requestingUser->getUserId());

        if (!$isSelfUser || !$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_ACCOUNT_OWN_UPLOAD_PROFILE_PICTURE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        if (!isset($uploadedFiles['profile_picture'])) {
            return Result::createError('No profile picture file provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $uploadedFiles['profile_picture'];

        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return Result::createError('File upload failed', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $mimeType = $uploadedFile->getClientMediaType();
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return Result::createError('Invalid file type. Allowed: JPEG, PNG, WebP', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if ($uploadedFile->getSize() > self::MAX_FILE_SIZE) {
            return Result::createError('File too large. Maximum size: 2MB', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $uploadDir = self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $userId = $requestingUser->getUserId()->asInt();
        $filename = $userId . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        $uploadedFile->moveTo($filepath);

        $this->userRepository->updateProfileImageId($requestingUser->getUserId(), $userId);

        return $this->responder->render($filename);
    }
}
