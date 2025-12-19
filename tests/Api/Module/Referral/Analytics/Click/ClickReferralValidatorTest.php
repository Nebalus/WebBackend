<?php

namespace UnitTesting\Api\Module\Referral\Analytics\Click;

use Nebalus\Webapi\Api\Module\Referral\Analytics\Click\ClickReferralValidator;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class ClickReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new ClickReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = ['referral_code' => '12345678'];

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(ReferralCode::class, $validator->getReferralCode());
        $this->assertEquals('12345678', $validator->getReferralCode()->asString());
    }
}
