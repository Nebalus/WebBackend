<?php

namespace Nebalus\Webapi\Repository;

use DateMalformedStringException;
use Nebalus\Webapi\Value\User\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\User\InvitationToken\InvitationTokens;
use Nebalus\Webapi\Value\User\InvitationToken\PureInvitationToken;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

readonly class MySqlUserInvitationTokenRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function findInvitationTokenByFields(PureInvitationToken $token): ?InvitationToken
    {
        $sql = "SELECT * FROM user_invitation_tokens WHERE token_field_1 = :token_field_1 AND token_field_2 = :token_field_2 AND token_field_3 = :token_field_3 AND token_field_4 = :token_field_4 AND token_field_5 = :token_field_5";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token_field_1', $token->getField1()->asInt());
        $stmt->bindValue(':token_field_2', $token->getField2()->asInt());
        $stmt->bindValue(':token_field_3', $token->getField3()->asInt());
        $stmt->bindValue(':token_field_4', $token->getField4()->asInt());
        $stmt->bindValue(':token_field_5', $token->getChecksumField()->asInt());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return InvitationToken::fromMySQL($data);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function getInvitationTokensFromOwnerUserId(UserId $ownerUserId): InvitationTokens
    {
        $sql = "SELECT * FROM `user_invitation_tokens` WHERE `owner_user_id` = :owner_user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = InvitationToken::fromMySQL($row);
        }

        return InvitationTokens::fromArray(...$data);
    }
}
