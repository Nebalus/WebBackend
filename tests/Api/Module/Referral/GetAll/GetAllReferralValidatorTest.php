<?php

namespace UnitTesting\Api\Module\Referral\GetAll;

use Nebalus\Webapi\Api\Module\Referral\GetAll\GetAllReferralValidator;
use Nebalus\Webapi\Value\User\UserId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class GetAllReferralValidatorTest extends TestCase
{
    #[Test]
    public function testValidatePassesWithValidData(): void
    {
        $validator = new GetAllReferralValidator();
        $request = $this->createMock(ServerRequest::class);
        $pathArgs = ['user_id' => 1];

        $validator->validate($request, $pathArgs);

        $this->assertInstanceOf(UserId::class, $validator->getUserId());
        $this->assertEquals(1, $validator->getUserId()->asInt());
    }
}
