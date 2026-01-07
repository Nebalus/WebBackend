<?php

namespace UnitTesting\Api\Admin\Role\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Admin\Role\Delete\DeleteRoleResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteRoleResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new DeleteRoleResponder();
        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Role Deleted', $result->getMessage());
    }
}
