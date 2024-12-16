<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Exception\SendMessageForbiddenException;
use Messenger\Domain\Request\SendMessageRequest;
use Messenger\Domain\Response\SendMessageResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\SendMessagePresenterTest;
use Messenger\Domain\UseCase\SendMessage;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;

class SendMessageTest extends TestCase
{
    private SendMessagePresenterTest $presenter;
    private UserRepository $userGateway;
    private DiscussionRepository $discussionGateway;
    private SendMessage $useCase;

    protected function setUp(): void
    {
        $this->presenter = new SendMessagePresenterTest();
        $this->userGateway = new UserRepository();
        $this->discussionGateway = new DiscussionRepository();
        $this->useCase = new SendMessage(new MessageRepository(), $this->discussionGateway);
    }

    public function testSuccessful(): void
    {
        $messageContent = "message content";
        $username = 'username1';
        $discussionId = "8dada3a9-f7fa-488c-9657-c4caa3fc2a35";
        $request = SendMessageRequest::create(
            $messageContent,
            $this->discussionGateway->find($discussionId),
            $this->userGateway->findOneByUsername($username));

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(SendMessageResponse::class, $this->presenter->response);

        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());
        $this->assertInstanceOf(Message::class, $this->presenter->response->getMessage());
        $this->assertEquals($discussionId, $this->presenter->response->getDiscussion()->getId()->toString());
        $this->assertEquals($messageContent, $this->presenter->response->getMessage()->getMessage());
        $this->assertEquals($username, $this->presenter->response->getMessage()->getAuthor()->getUsername());
        $this->assertEquals($discussionId, $this->presenter->response->getMessage()->getDiscussion()->getId()->toString());
    }


    /**
     * @dataProvider provideFailedValidationRequestsData
     * @param string|null $messageContent
     * @param string $discussionId
     * @param string $username
     * @return void
     * @throws AssertionFailedException
     * @throws NotAMemberOfTheDiscussionException
     * @throws SendMessageForbiddenException
     */
    public function testFailedValidation(?string $messageContent, string $discussionId, string $username, string $exception): void
    {
        $this->expectException($exception);
        SendMessageRequest::create(
            $messageContent,
            $this->discussionGateway->find($discussionId),
            $this->userGateway->findOneByUsername($username));
    }

    public function provideFailedValidationRequestsData(): \Generator
    {
        yield ["", "8dada3a9-f7fa-488c-9657-c4caa3fc2a35", 'username1', AssertionFailedException::class];
        yield ["", "8dada3a9-f7fa-488c-9657-c4caa3fc2a35", 'username2', NotAMemberOfTheDiscussionException::class];
    }
}
