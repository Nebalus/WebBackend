<?php

namespace UnitTesting\Api\Module\Referral\Edit;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralService;
use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EditReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private EditReferralResponder $responder;
    private EditReferralService $service;
    private EditReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(EditReferralResponder::class);
        $this->service = new EditReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(EditReferralValidator::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionWhenUserHasNoAccess(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $requestingUserId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($requestingUserId);

        $validatorUserId = $this->createMock(UserId::class);
        $this->validator->method('getUserId')->willReturn($validatorUserId);

        $validatorUserId->method('equals')->with($requestingUserId)->willReturn(false);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteReturnsErrorWhenReferralDoesNotExist(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $requestingUserId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($requestingUserId);

        $validatorUserId = $this->createMock(UserId::class);
        $this->validator->method('getUserId')->willReturn($validatorUserId);

        $validatorUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $referralCode = $this->createMock(ReferralCode::class);
        $this->validator->method('getCode')->willReturn($referralCode);

        $url = $this->createMock(Url::class);
        $this->validator->method('getUrl')->willReturn($url);

        $label = $this->createMock(ReferralLabel::class);
        $this->validator->method('getLabel')->willReturn($label);

        $this->validator->method('isDisabled')->willReturn(false);

        $this->referralRepository->method('updateReferralFromOwner')
            ->with($requestingUserId, $referralCode, $url, $label, false)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral does not exist', $result->getMessage());
    }

    #[Test]
    public function testExecuteUpdatesReferralAndReturnsResultWhenAuthorized(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $requestingUserId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($requestingUserId);

        $validatorUserId = $this->createMock(UserId::class);
        $this->validator->method('getUserId')->willReturn($validatorUserId);

        $validatorUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $referralCode = $this->createMock(ReferralCode::class);
        $this->validator->method('getCode')->willReturn($referralCode);

        $url = $this->createMock(Url::class);
        $this->validator->method('getUrl')->willReturn($url);

        $label = $this->createMock(ReferralLabel::class);
        $this->validator->method('getLabel')->willReturn($label);

        $this->validator->method('isDisabled')->willReturn(false);

        $referral = $this->createMock(Referral::class);
        $referral->method('getOwnerId')->willReturn($requestingUserId);
        $requestingUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $this->referralRepository->method('updateReferralFromOwner')
            ->with($requestingUserId, $referralCode, $url, $label, false)
            ->willReturn($referral);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($referral)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
