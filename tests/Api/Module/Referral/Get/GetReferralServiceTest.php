<?php

namespace UnitTesting\Api\Module\Referral\Get;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralService;
use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralValidator;
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

class GetReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private GetReferralResponder $responder;
    private GetReferralService $service;
    private GetReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(GetReferralResponder::class);
        $this->service = new GetReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(GetReferralValidator::class);
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
        $this->validator->method('getReferralCode')->willReturn($referralCode);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn(null);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral does not exist', $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsReferralWhenAuthorizedAndFound(): void
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

        $referral = $this->createMock(Referral::class);
        $referral->method('getOwnerId')->willReturn($requestingUserId);
        $requestingUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
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
