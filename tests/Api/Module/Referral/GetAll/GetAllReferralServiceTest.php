<?php

namespace UnitTesting\Api\Module\Referral\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralService;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private GetAllReferralResponder $responder;
    private GetAllReferralService $service;
    private GetAllReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(GetAllReferralResponder::class);
        $this->service = new GetAllReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(GetAllReferralValidator::class);
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
    public function testExecuteReturnsReferralsWhenAuthorized(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $requestingUserId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($requestingUserId);

        $validatorUserId = $this->createMock(UserId::class);
        $this->validator->method('getUserId')->willReturn($validatorUserId);

        $validatorUserId->method('equals')->with($requestingUserId)->willReturn(true);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $referrals = $this->createMock(ReferralCollection::class);
        $this->referralRepository->method('findReferralsFromOwner')
            ->with($requestingUserId)
            ->willReturn($referrals);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($referrals)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
