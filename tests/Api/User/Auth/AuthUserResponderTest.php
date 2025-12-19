<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Auth;

use DateTimeImmutable;
use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\User\Auth\AuthUserResponder;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\Username;
use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Jwt;

class AuthUserResponderTest extends TestCase
{
    private AuthUserResponder $responder;

    protected function setUp(): void
    {
        $this->responder = new AuthUserResponder();
    }

    public function testRender(): void
    {
        $jwt = $this->createMock(Jwt::class);
        $user = $this->createMock(UserAccount::class);
        $username = $this->createMock(Username::class);
        $email = $this->createMock(UserEmail::class);
        $date = new DateTimeImmutable('2023-01-01 00:00:00');

        $jwt->expects($this->once())
            ->method('getToken')
            ->willReturn('jwt_token');

        $user->expects($this->once())
            ->method('getUsername')
            ->willReturn($username);
        $username->expects($this->once())
            ->method('asString')
            ->willReturn('testuser');

        $user->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $email->expects($this->once())
            ->method('asString')
            ->willReturn('test@example.com');

        $user->expects($this->once())
            ->method('isDisabled')
            ->willReturn(false);

        $user->expects($this->once())
            ->method('getCreatedAtDate')
            ->willReturn($date);

        $user->expects($this->once())
            ->method('getUpdatedAtDate')
            ->willReturn($date);

        $result = $this->responder->render($jwt, $user);

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'message' => 'User authenticated',
            'status_code' => 200,
            'payload' => [
                'jwt' => 'jwt_token',
                'user' => [
                    'username' => 'testuser',
                    'email' => 'test@example.com',
                    'disabled' => false,
                    'created_at' => $date->format(DATE_ATOM),
                    'updated_at' => $date->format(DATE_ATOM),
                ]
            ]
        ], $result->getPayload());
    }
}
