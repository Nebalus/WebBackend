<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Auth;

use Monolog\Logger;
use Nebalus\Webapi\Api\User\Auth\AuthUserResponder;
use Nebalus\Webapi\Api\User\Auth\AuthUserService;
use Nebalus\Webapi\Api\User\Auth\AuthUserValidator;
use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Utils\IpUtils;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReallySimpleJWT\Jwt;
use Resend\Client as ResendClient;
use Resend\Service\Email;
use Twig\Environment as TwigEnvironment;

class AuthUserServiceTest extends TestCase
{
    private AuthUserService $service;
    private MySqlUserRepository&MockObject $userRepository;
    private GeneralConfig&MockObject $config;
    private AuthUserResponder&MockObject $responder;
    private ResendClient&MockObject $resendClient;
    private TwigEnvironment&MockObject $twig;
    private IpUtils&MockObject $ipUtils;
    private Logger&MockObject $logger;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
        $this->config = $this->createMock(GeneralConfig::class);
        $this->responder = $this->createMock(AuthUserResponder::class);
        $this->resendClient = $this->createMock(ResendClient::class);
        $this->twig = $this->createMock(TwigEnvironment::class);
        $this->ipUtils = $this->createMock(IpUtils::class);
        $this->logger = $this->createMock(Logger::class);

        $this->service = new AuthUserService(
            $this->userRepository,
            $this->config,
            $this->responder,
            $this->resendClient,
            $this->twig,
            $this->ipUtils,
            $this->logger
        );
    }

    public function testExecuteSuccess(): void
    {
        $validator = $this->createMock(AuthUserValidator::class);
        $user = $this->createMock(UserAccount::class);
        $username = $this->createMock(Username::class);
        $password = $this->createMock(UserPassword::class);
        $email = $this->createMock(UserEmail::class);
        $userId = $this->createMock(UserId::class);
        $emailsService = $this->createMock(Email::class);

        $validator->expects($this->once())
            ->method('getUsername')
            ->willReturn($username);
        $validator->expects($this->once())
            ->method('getPassword')
            ->willReturn('password');

        $this->userRepository->expects($this->once())
            ->method('findUserFromUsername')
            ->with($username)
            ->willReturn($user);

        $user->expects($this->once())
            ->method('isDisabled')
            ->willReturn(false);
        $user->expects($this->once())
            ->method('getPassword')
            ->willReturn($password);
        $password->expects($this->once())
            ->method('verify')
            ->with('password')
            ->willReturn(true);
        $user->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $email->expects($this->any())
            ->method('asString')
            ->willReturn('test@example.com');
        $user->expects($this->any())
            ->method('getUsername')
            ->willReturn($username);
        $username->expects($this->any())
            ->method('asString')
            ->willReturn('testuser');
        $user->expects($this->any())
            ->method('getUserId')
            ->willReturn($userId);
        $userId->expects($this->any())
            ->method('asInt')
            ->willReturn(1);

        $this->resendClient->emails = $emailsService;
        $emailsService->expects($this->once())
            ->method('send');

        $this->twig->expects($this->once())
            ->method('render')
            ->willReturn('email content');

        $this->ipUtils->expects($this->once())
            ->method('getClientIP')
            ->willReturn('127.0.0.1');

        $this->config->expects($this->once())
            ->method('getJwtExpirationTime')
            ->willReturn(3600);
        $this->config->expects($this->once())
            ->method('getJwtSecret')
            ->willReturn('!Secr3tP@ssw0rdWithMoreThan32CharsToBeSafe!');

        $this->responder->expects($this->once())
            ->method('render')
            ->with($this->isInstanceOf(Jwt::class), $user)
            ->willReturn($this->createMock(ResultInterface::class));

        $this->service->execute($validator);
    }

    public function testExecuteFailure(): void
    {
        $validator = $this->createMock(AuthUserValidator::class);
        $username = $this->createMock(Username::class);

        $validator->expects($this->once())
            ->method('getUsername')
            ->willReturn($username);

        $this->userRepository->expects($this->once())
            ->method('findUserFromUsername')
            ->with($username)
            ->willReturn(null);

        $result = $this->service->execute($validator);
        $this->assertEquals(401, $result->getStatusCode());
    }
}
