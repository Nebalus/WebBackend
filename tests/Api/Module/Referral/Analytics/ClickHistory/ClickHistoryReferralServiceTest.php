<?php

namespace UnitTesting\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralService;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickHistoryReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private ClickHistoryReferralResponder $responder;
    private ClickHistoryReferralService $service;
    private ClickHistoryReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(ClickHistoryReferralResponder::class);
        $this->service = new ClickHistoryReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(ClickHistoryReferralValidator::class);
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
    public function testExecuteReturnsErrorWhenReferralNotFound(): void
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
        $this->validator->method('getReferralCode')->willReturn($referralCode);
        $this->validator->method('getRange')->willReturn(7);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral not found', $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsSuccessWhenAuthorizedAndFound(): void
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
        $this->validator->method('getReferralCode')->willReturn($referralCode);
        $this->validator->method('getRange')->willReturn(7);

        $referral = $this->createMock(Referral::class);
        $referral->method('getOwnerId')->willReturn($requestingUserId);
        $requestingUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn($referral);

        $data = $this->createMock(\Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickCollection::class);
        $this->referralRepository->method('getReferralClicksFromRange')
            ->with($requestingUserId, $referralCode, 7)
            ->willReturn($data);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($referralCode, $data)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
