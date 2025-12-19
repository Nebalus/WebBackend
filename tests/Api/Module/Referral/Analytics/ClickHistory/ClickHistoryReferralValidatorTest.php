<?php

namespace UnitTesting\Api\Module\Referral\Analytics\ClickHistory;

use Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory\ClickHistoryReferralValidator;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class ClickHistoryReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new ClickHistoryReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [
            'referral_code' => '12345678',
            'user_id' => 1
        ];
        $queryParams = ['range' => 7];

        $request->method('getQueryParams')->willReturn($queryParams);

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(ReferralCode::class, $validator->getReferralCode());
        $this->assertEquals('12345678', $validator->getReferralCode()->asString());

        $this->assertInstanceOf(UserId::class, $validator->getUserId());
        $this->assertEquals(1, $validator->getUserId()->asInt());

        $this->assertEquals(7, $validator->getRange());
    }
}
