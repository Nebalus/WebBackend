<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Register;

use Nebalus\Webapi\Api\User\Register\RegisterUserValidator;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenValue;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\Username;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class RegisterUserValidatorTest extends TestCase
{
    private RegisterUserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RegisterUserValidator();
    }

    public function testValidate(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'invitation_token' => 'valid_token',
                'email' => 'test@example.com',
                'username' => 'testuser',
                'password' => 'Password123!'
            ]);

        $this->validator->validate($request, []);

        $this->assertInstanceOf(InvitationTokenValue::class, $this->validator->getPureInvitationToken());
        $this->assertEquals('valid_token', $this->validator->getPureInvitationToken()->asString());

        $this->assertInstanceOf(UserEmail::class, $this->validator->getUserEmail());
        $this->assertEquals('test@example.com', $this->validator->getUserEmail()->asString());

        $this->assertInstanceOf(Username::class, $this->validator->getUsername());
        $this->assertEquals('testuser', $this->validator->getUsername()->asString());

        $this->assertInstanceOf(UserPassword::class, $this->validator->getUserPassword());
        // UserPassword::fromPlain hashes the password, so we can't assert equality easily without verifying hash.
        // But we can check if it's an instance.
    }
}
