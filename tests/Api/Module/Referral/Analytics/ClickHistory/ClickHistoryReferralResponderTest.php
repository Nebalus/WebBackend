<?php

namespace UnitTesting\Api\Module\Referral\Analytics\ClickHistory;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralResponder;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClick;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickCollection;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClickHistoryReferralResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $responder = new ClickHistoryReferralResponder();
        $referralCode = $this->createMock(ReferralCode::class);
        $referralCode->method('asString')->willReturn('CODE1234');

        $referralClick = $this->createMock(ReferralClick::class);
        $referralClick->method('getClickedAtDate')->willReturn(new \DateTimeImmutable('2023-01-01'));
        $clickCount = $this->createMock(\Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickAmount::class);
        $clickCount->method('asInt')->willReturn(10);
        $referralClick->method('getClickCount')->willReturn($clickCount);
        $uniqueVisitors = $this->createMock(\Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickAmount::class);
        $uniqueVisitors->method('asInt')->willReturn(5);
        $referralClick->method('getUniqueVisitorsCount')->willReturn($uniqueVisitors);

        $referralClicks = $this->createMock(ReferralClickCollection::class);
        $referralClicks->method('getIterator')->willReturn(new \ArrayIterator([$referralClick]));

        $result = $responder->render($referralCode, $referralClicks);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Referral history found', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertEquals('CODE1234', $data['code']);
        $this->assertCount(1, $data['history']);
        $this->assertEquals('2023-01-01', $data['history'][0]['date']);
        $this->assertEquals(10, $data['history'][0]['count']);
        $this->assertEquals(5, $data['history'][0]['unique_visitors']);
    }
}
