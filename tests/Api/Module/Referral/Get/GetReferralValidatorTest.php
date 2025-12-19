<?php

namespace UnitTesting\Api\Module\Referral\Get;

use Nebalus\Webapi\Api\Module\Referral\Get\GetReferralValidator;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new GetReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [
            'referral_code' => '12345678',
            'user_id' => 1
        ];

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(ReferralCode::class, $validator->getReferralCode());
        $this->assertEquals('12345678', $validator->getReferralCode()->asString());

        $this->assertInstanceOf(UserId::class, $validator->getUserId());
        $this->assertEquals(1, $validator->getUserId()->asInt());
    }
}
