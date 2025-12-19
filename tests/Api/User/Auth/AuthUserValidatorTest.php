<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Auth;

use Nebalus\Webapi\Api\User\Auth\AuthUserValidator;
use Nebalus\Webapi\Value\User\Authentication\Totp\TOTPCode;
use Nebalus\Webapi\Value\User\Username;
use PHPUnit\Framework\TestCase;
use Slim\Http\ServerRequest;

class AuthUserValidatorTest extends TestCase
{
    private AuthUserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AuthUserValidator();
    }

    public function testValidate(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'username' => 'testuser',
                'password' => 'Password123!',
                'remember_me' => true,
                'totp' => '123456'
            ]);

        $this->validator->validate($request, []);

        $this->assertInstanceOf(Username::class, $this->validator->getUsername());
        $this->assertEquals('testuser', $this->validator->getUsername()->asString());
        $this->assertEquals('Password123!', $this->validator->getPassword());
        $this->assertTrue($this->validator->getRememberMe());
        $this->assertInstanceOf(TOTPCode::class, $this->validator->getTOTPCode());
        $this->assertEquals('123456', $this->validator->getTOTPCode()->asString());
    }

    public function testValidateWithoutTotp(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                'username' => 'testuser',
                'password' => 'Password123!',
                'remember_me' => false
            ]);

        $this->validator->validate($request, []);

        $this->assertNull($this->validator->getTOTPCode());
    }
}
