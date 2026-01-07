<?php

namespace UnitTesting\Api\Admin\User\GetAll;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\User\GetAll\GetAllUserResponder;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;
use Nebalus\Webapi\Value\User\Authentication\Totp\TOTPSecretKey;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetAllUserResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResultWithCorrectData(): void
    {
        $user1 = UserAccount::from(
            UserId::from(1),
            Username::from('userone'),
            UserEmail::from('userone@example.com'),
            UserPassword::fromHash('hashed_password'),
            TOTPSecretKey::from('00000000000000000000000000000000'),
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $user2 = UserAccount::from(
            UserId::from(2),
            Username::from('usertwo'),
            UserEmail::from('usertwo@example.com'),
            UserPassword::fromHash('hashed_password'),
            TOTPSecretKey::from('00000000000000000000000000000000'),
            true,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $responder = new GetAllUserResponder();
        $result = $responder->render([$user1, $user2]);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('List of all users', $result->getMessage());

        $payload = $result->getPayload();
        $data = $payload['payload'];

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['user_id']);
        $this->assertEquals('userone', $data[0]['username']);
        $this->assertEquals(2, $data[1]['user_id']);
        $this->assertEquals('usertwo', $data[1]['username']);
    }
}
