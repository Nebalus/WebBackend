<?php

namespace UnitTesting\Api\Module\Referral\Delete;

use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralAction;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralService;
use Nebalus\Webapi\Api\Module\Referral\Delete\DeleteReferralValidator;
use Nebalus\Webapi\Config\Types\AttributeTypes;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class DeleteReferralActionTest extends TestCase
{
    private DeleteReferralService $service;
    private DeleteReferralValidator $validator;
    private DeleteReferralAction $action;

    protected function setUp(): void
    {
        $this->service = $this->createMock(DeleteReferralService::class);
        $this->validator = $this->createMock(DeleteReferralValidator::class);
        $this->action = new DeleteReferralAction($this->service, $this->validator);
    }

    #[Test]
    public function testInvokeExecutesServiceAndReturnsResponse(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $response = $this->createMock(Response::class);
        $pathArgs = [];

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($request, $pathArgs);

        $requestingUser = $this->createMock(UserAccount::class);
        $userPerms = $this->createMock(UserPermissionIndex::class);

        $request->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturnMap([
                [AttributeTypes::CLIENT_USER, null, $requestingUser],
                [AttributeTypes::CLIENT_USER_PERMISSION_INDEX, null, $userPerms],
            ]);

        $result = $this->createMock(ResultInterface::class);
        $result->method('getPayload')->willReturn(['data' => 'test']);
        $result->method('getStatusCode')->willReturn(200);

        $this->service->expects($this->once())
            ->method('execute')
            ->with($this->validator, $requestingUser, $userPerms)
            ->willReturn($result);

        $response->expects($this->once())
            ->method('withJson')
            ->with(['data' => 'test'], 200)
            ->willReturnSelf();

        $this->action->__invoke($request, $response, $pathArgs);
    }
}
