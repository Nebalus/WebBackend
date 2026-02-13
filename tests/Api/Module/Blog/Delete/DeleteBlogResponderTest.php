<?php

namespace UnitTesting\Api\Module\Blog\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Api\Module\Blog\Delete\DeleteBlogResponder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteBlogResponderTest extends TestCase
{
    #[Test]
    public function testRenderReturnsSuccessResult(): void
    {
        $responder = new DeleteBlogResponder();

        $result = $responder->render();

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $result->getStatusCode());
        $this->assertEquals('Blog Deleted', $result->getMessage());
    }
}
