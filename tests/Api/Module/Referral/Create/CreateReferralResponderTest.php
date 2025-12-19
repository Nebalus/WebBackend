<?php

namespace UnitTesting\Api\Module\Referral\Create;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralResponder;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateReferralResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new CreateReferralResponder();
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

        $result = $responder->render($referral);

        $this->assertEquals(StatusCodeInterface::STATUS_CREATED, $result->getStatusCode());
        $this->assertEquals('Referral Created', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals('CODE1234', $data['code']);
        $this->assertEquals('http://example.com', $data['url']);
        $this->assertEquals('My Referral', $data['label']);
        $this->assertFalse($data['disabled']);
    }
}
