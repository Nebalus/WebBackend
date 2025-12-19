<?php

namespace UnitTesting\Api\Module\Referral\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralService;
use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralValidator;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private CreateReferralResponder $responder;
    private CreateReferralService $service;
    private CreateReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(CreateReferralResponder::class);
        $this->service = new CreateReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(CreateReferralValidator::class);
    }

    #[Test]
    public function testExecuteReturnsNoPermissionWhenUserHasNoAccess(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(false);

        $result = $this->service->execute($this->validator, $requestingUser, $userPerms);

        $this->assertEquals(StatusCodeInterface::STATUS_FORBIDDEN, $result->getStatusCode());
    }

    #[Test]
    public function testExecuteCreatesReferralAndReturnsResultWhenAuthorized(): void
    {
        $requestingUser = $this->createMock(UserAccount::class);
        $userId = $this->createMock(UserId::class);
        $requestingUser->method('getUserId')->willReturn($userId);

        $userPerms = $this->createMock(UserPermissionIndex::class);
        $userPerms->method('hasAccessTo')->willReturn(true);

        $url = $this->createMock(Url::class);
        $this->validator->method('getUrl')->willReturn($url);

        $label = $this->createMock(ReferralLabel::class);
        $this->validator->method('getLabel')->willReturn($label);

        $this->validator->method('isDisabled')->willReturn(false);

        $this->referralRepository->expects($this->once())
            ->method('insertReferral')
            ->with($userId, $this->anything(), $url, $label, false);

        $referral = $this->createMock(Referral::class);
        $this->referralRepository->method('findReferralByCode')
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
