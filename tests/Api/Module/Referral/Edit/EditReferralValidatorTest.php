<?php

namespace UnitTesting\Api\Module\Referral\Edit;

use Nebalus\Webapi\Api\Module\Referral\Edit\EditReferralValidator;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class EditReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new EditReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = [
            'referral_code' => '12345678',
            'user_id' => 1
        ];
        $body = [
            'label' => 'My Label',
            'url' => 'http://example.com',
            'disabled' => false
        ];

        $request->method('getParsedBody')->willReturn($body);

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $validator->getUserId());
        $this->assertEquals(1, $validator->getUserId()->asInt());

        $this->assertInstanceOf(ReferralCode::class, $validator->getCode());
        $this->assertEquals('12345678', $validator->getCode()->asString());

        $this->assertInstanceOf(ReferralLabel::class, $validator->getLabel());
        $this->assertEquals('My Label', $validator->getLabel()->asString());

        $this->assertInstanceOf(Url::class, $validator->getUrl());
        $this->assertEquals('http://example.com', $validator->getUrl()->asString());

        $this->assertFalse($validator->isDisabled());
    }
}
