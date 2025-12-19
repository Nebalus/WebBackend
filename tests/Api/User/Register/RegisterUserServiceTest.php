<?php

declare(strict_types=1);

namespace UnitTesting\Api\User\Register;

use Monolog\Logger;
use Nebalus\Webapi\Api\User\Register\RegisterUserResponder;
use Nebalus\Webapi\Api\User\Register\RegisterUserService;
use Nebalus\Webapi\Api\User\Register\RegisterUserValidator;
use Nebalus\Webapi\Repository\AccountRepository\MySqlAccountRepository;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenValue;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\Username;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Resend\Client as ResendClient;
use Resend\Service\Email;
use Twig\Environment as TwigEnvironment;

class RegisterUserServiceTest extends TestCase
{
    private RegisterUserService $service;
    private MySqlUserRepository&MockObject $userRepository;
    private MySqlAccountRepository&MockObject $accountRepository;
    private RegisterUserResponder&MockObject $responder;
    private ResendClient&MockObject $resendClient;
    private TwigEnvironment&MockObject $twig;
    private Logger&MockObject $logger;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(MySqlUserRepository::class);
        $this->accountRepository = $this->createMock(MySqlAccountRepository::class);
        $this->responder = $this->createMock(RegisterUserResponder::class);
        $this->resendClient = $this->createMock(ResendClient::class);
        $this->twig = $this->createMock(TwigEnvironment::class);
        $this->logger = $this->createMock(Logger::class);

        $this->service = new RegisterUserService(
            $this->userRepository,
            $this->accountRepository,
            $this->responder,
            $this->resendClient,
            $this->twig,
            $this->logger
        );
    }

    public function testExecuteSuccess(): void
    {
        $validator = $this->createMock(RegisterUserValidator::class);
        $invitationTokenValue = $this->createMock(InvitationTokenValue::class);
        $invitationToken = $this->createMock(InvitationToken::class);
        $username = $this->createMock(Username::class);
        $email = $this->createMock(UserEmail::class);
        $password = $this->createMock(UserPassword::class);
        $user = $this->createMock(UserAccount::class);
        $emailsService = $this->createMock(Email::class);

        $validator->expects($this->once())
            ->method('getPureInvitationToken')
            ->willReturn($invitationTokenValue);
        $validator->expects($this->exactly(2))
            ->method('getUsername')
            ->willReturn($username);
        $validator->expects($this->once())
            ->method('getUserEmail')
            ->willReturn($email);
        $validator->expects($this->once())
            ->method('getUserPassword')
            ->willReturn($password);

        $this->accountRepository->expects($this->once())
            ->method('findInvitationTokenByFields')
            ->with($invitationTokenValue)
            ->willReturn($invitationToken);

        $invitationToken->expects($this->once())
            ->method('isExpired')
            ->willReturn(false);

        $this->userRepository->expects($this->once())
            ->method('findUserFromUsername')
            ->with($username)
            ->willReturn(null);

        $this->userRepository->expects($this->once())
            ->method('findUserFromEmail')
            ->with($email)
            ->willReturn(null);

        $this->userRepository->expects($this->once())
            ->method('registerUser')
            ->with($this->isInstanceOf(UserAccount::class), $invitationToken)
            ->willReturn($user);

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

        $this->resendClient->emails = $emailsService;
        $emailsService->expects($this->once())
            ->method('send');

        $this->twig->expects($this->once())
            ->method('render')
            ->willReturn('email content');

        $this->responder->expects($this->once())
            ->method('render')
            ->willReturn($this->createMock(ResultInterface::class));

        $this->service->execute($validator);
    }

    public function testExecuteTokenNotFound(): void
    {
        $validator = $this->createMock(RegisterUserValidator::class);
        $invitationTokenValue = $this->createMock(InvitationTokenValue::class);

        $validator->expects($this->once())
            ->method('getPureInvitationToken')
            ->willReturn($invitationTokenValue);

        $this->accountRepository->expects($this->once())
            ->method('findInvitationTokenByFields')
            ->with($invitationTokenValue)
            ->willReturn(null);

        $result = $this->service->execute($validator);
        $this->assertEquals(403, $result->getStatusCode());
    }
}
