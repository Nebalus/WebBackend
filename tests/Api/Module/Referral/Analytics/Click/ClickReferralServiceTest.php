<?php

namespace UnitTesting\Api\Module\Referral\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralResponder;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralService;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralValidator;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickReferralServiceTest extends TestCase
{
    private MySqlReferralRepository $referralRepository;
    private ClickReferralResponder $responder;
    private ClickReferralService $service;
    private ClickReferralValidator $validator;

    protected function setUp(): void
    {
        $this->referralRepository = $this->createMock(MySqlReferralRepository::class);
        $this->responder = $this->createMock(ClickReferralResponder::class);
        $this->service = new ClickReferralService($this->referralRepository, $this->responder);
        $this->validator = $this->createMock(ClickReferralValidator::class);
    }

    #[Test]
    public function testExecuteReturnsErrorWhenReferralNotFound(): void
    {
        $referralCode = $this->createMock(ReferralCode::class);
        $this->validator->method('getReferralCode')->willReturn($referralCode);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn(null);

        $result = $this->service->execute($this->validator);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral code not found', $result->getMessage());
    }

    #[Test]
    public function testExecuteReturnsErrorWhenReferralIsDisabled(): void
    {
        $referralCode = $this->createMock(ReferralCode::class);
        $this->validator->method('getReferralCode')->willReturn($referralCode);

        $referral = $this->createMock(Referral::class);
        $referral->method('isDisabled')->willReturn(true);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn($referral);

        $result = $this->service->execute($this->validator);

        $this->assertEquals(StatusCodeInterface::STATUS_NOT_FOUND, $result->getStatusCode());
        $this->assertEquals('Referral code not found', $result->getMessage());
    }

    #[Test]
    public function testExecuteInsertsClickAndReturnsResultFromResponder(): void
    {
        $referralCode = $this->createMock(ReferralCode::class);
        $this->validator->method('getReferralCode')->willReturn($referralCode);

        $referral = $this->createMock(Referral::class);
        $referral->method('isDisabled')->willReturn(false);
        $referralId = $this->createMock(ReferralId::class);
        $referral->method('getReferralId')->willReturn($referralId);

        $this->referralRepository->method('findReferralByCode')
            ->with($referralCode)
            ->willReturn($referral);

        $this->referralRepository->expects($this->once())
            ->method('insertReferralClickEntry')
            ->with($referralId);

        $expectedResult = $this->createMock(ResultInterface::class);
        $this->responder->expects($this->once())
            ->method('render')
            ->with($referral)
            ->willReturn($expectedResult);

        $result = $this->service->execute($this->validator);

        $this->assertSame($expectedResult, $result);
    }
}
