<?php

namespace Nebalus\Webapi\Api\User\Register;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenField;
use Nebalus\Webapi\Value\Account\InvitationToken\PureInvitationToken;
use Nebalus\Webapi\Value\Internal\Validation\ValidRequestData;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\Username;
use Nebalus\Webapi\Value\User\UserPassword;

class RegisterUserValidator extends AbstractValidator
{
    private PureInvitationToken $pureInvitationToken;
    private UserEmail $userEmail;
    private Username $username;
    private UserPassword $userPassword;

    public function __construct()
    {
        $rules = [
            'body' => S::object([
                'invitation_token' => S::object([
                    "field_1" => S::number()->integer(),
                    "field_2" => S::number()->integer(),
                    "field_3" => S::number()->integer(),
                    "field_4" => S::number()->integer(),
                    "checksum" => S::number()->integer(),
                ]),
                'email' => S::string()->email(),
                'username' => S::string(),
                'password' => S::string(),
            ])
        ];
        parent::__construct($rules);
    }

    /**
     * @throws ApiException
     */
    protected function onValidate(ValidRequestData $request): void
    {
        $this->pureInvitationToken = PureInvitationToken::from(
            InvitationTokenField::from($request->getBodyData()["invitation_token"]["field_1"]),
            InvitationTokenField::from($request->getBodyData()["invitation_token"]["field_2"]),
            InvitationTokenField::from($request->getBodyData()["invitation_token"]["field_3"]),
            InvitationTokenField::from($request->getBodyData()["invitation_token"]["field_4"]),
            InvitationTokenField::from($request->getBodyData()["invitation_token"]["checksum"])
        );
        $this->userEmail = UserEmail::from($request->getBodyData()["email"]);
        $this->username = UserName::from($request->getBodyData()["username"]);
        $this->userPassword = UserPassword::fromPlain($request->getBodyData()["password"]);
    }

    public function getPureInvitationToken(): PureInvitationToken
    {
        return $this->pureInvitationToken;
    }

    public function getUserEmail(): UserEmail
    {
        return $this->userEmail;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getUserPassword(): UserPassword
    {
        return $this->userPassword;
    }
}
