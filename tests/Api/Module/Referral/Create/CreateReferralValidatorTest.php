<?php

namespace UnitTesting\Api\Module\Referral\Create;

use Nebalus\Webapi\Api\Module\Referral\Create\CreateReferralValidator;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class CreateReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new CreateReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = ['user_id' => 1];
        $body = [
            'label' => 'My Label',
            'url' => 'http://example.com',
            'disabled' => false
        ];

        $request->method('getParsedBody')->willReturn($body);

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $validator->getUserId());
        $this->assertEquals(1, $validator->getUserId()->asInt());

        $this->assertInstanceOf(ReferralLabel::class, $validator->getLabel());
        $this->assertEquals('My Label', $validator->getLabel()->asString());

        $this->assertInstanceOf(Url::class, $validator->getUrl());
        $this->assertEquals('http://example.com', $validator->getUrl()->asString());

        $this->assertFalse($validator->isDisabled());
    }
}
