<?php

namespace UnitTesting\Api\Module\Referral\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralResponder;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickReferralResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new ClickReferralResponder();
        $referral = $this->createMock(Referral::class);
        $referralUrl = $this->createMock(Url::class);

        $referralUrl->method('asString')->willReturn('http://example.com');
        $referral->method('getUrl')->willReturn($referralUrl);

        $result = $responder->render($referral);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Referral found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals('http://example.com', $data['url']);
    }
}
