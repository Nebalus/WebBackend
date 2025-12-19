<?php

namespace UnitTesting\Api\Module\Referral\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralResponder;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralCollection;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllReferralResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new GetAllReferralResponder();

        $referral = $this->createMock(Referral::class);
        $code = $this->createMock(ReferralCode::class);
        $code->method('asString')->willReturn('CODE1234');
        $referral->method('getCode')->willReturn($code);

        $url = $this->createMock(Url::class);
        $url->method('asString')->willReturn('http://example.com');
        $referral->method('getUrl')->willReturn($url);

        $label = $this->createMock(ReferralLabel::class);
        $label->method('asString')->willReturn('My Referral');
        $referral->method('getLabel')->willReturn($label);

        $referral->method('isDisabled')->willReturn(false);
        $referral->method('getCreatedAtDate')->willReturn(new \DateTimeImmutable('2023-01-01'));
        $referral->method('getUpdatedAtDate')->willReturn(new \DateTimeImmutable('2023-01-02'));

        $referrals = $this->createMock(ReferralCollection::class);
        $referrals->method('getIterator')->willReturn(new \ArrayIterator([$referral]));

        $result = $responder->render($referrals);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('List of referrals found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(1, $data);
        $this->assertEquals('CODE1234', $data[0]['code']);
        $this->assertEquals('http://example.com', $data[0]['url']);
        $this->assertEquals('My Referral', $data[0]['label']);
        $this->assertFalse($data[0]['disabled']);
    }
}
