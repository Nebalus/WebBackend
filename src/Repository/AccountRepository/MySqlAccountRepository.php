<?php

namespace Nebalus\Webapi\Repository\AccountRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Account\AccountId;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenCollection;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationTokenValue;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

readonly class MySqlAccountRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * @throws ApiException
     */
    public function insertAccount(UserId $userId): AccountId
    {
        $sql = <<<SQL
            INSERT INTO 
                accounts(user_id) 
            VALUES
                (:user_id)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return AccountId::from($this->pdo->lastInsertId());
    }

    /**
     * @throws ApiException
     */
    public function updateInvitationToken(InvitationToken $invitationToken): void
    {
        $sql = <<<SQL
            UPDATE account_invitation_tokens
            SET 
                invited_id = :invited_id,
                used_at = :used_at 
            WHERE 
                token = :token
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':invited_id', $invitationToken->getInvitedId()->asInt());
        $stmt->bindValue(':used_at', $invitationToken->getUsedAtDate()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':token', $invitationToken->getInvitationTokenValue()->asString());
        $stmt->execute();
    }

    /**
     * @throws ApiException
     */
    public function findInvitationTokenByFields(InvitationTokenValue $token): ?InvitationToken
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM account_invitation_tokens 
            WHERE 
                token = :token 
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token', $token->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return InvitationToken::fromDatabase($data);
    }

    /**
     * @throws ApiException
     */
    public function getInvitationTokensFromOwnerId(AccountId $ownerId): InvitationTokenCollection
    {
        $data = [];
        $sql = <<<SQL
            SELECT
                * 
            FROM account_invitation_tokens 
            WHERE 
                owner_id = :owner_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $data[] = InvitationToken::fromArray($row);
        }
        return InvitationTokenCollection::fromObjects(...$data);
    }
}
