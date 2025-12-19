<?php

namespace UnitTesting\Api\Module\Referral\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralService;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private DeleteReferralResponder $responder;
    private DeleteReferralService $service;
    private DeleteReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(DeleteReferralResponder::class);
        $this->service = new DeleteReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(DeleteReferralValidator::class);
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

        $this->referralRepository->method('deleteReferralByCodeFromOwner')
            ->with($requestingUserId, $referralCode)
            ->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral does not exist', $result->getMessage());
    }

    #[Test]
    public function testExecuteDeletesReferralAndReturnsResultWhenAuthorized(): void
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

        $this->referralRepository->method('deleteReferralByCodeFromOwner')
            ->with($requestingUserId, $referralCode)
            ->willReturn(true);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertSame($expectedResult, $result);
    }
}
