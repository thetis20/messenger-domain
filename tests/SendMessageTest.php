<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Exception\SendMessageForbiddenException;
use Messenger\Domain\RequestFactory\SendMessageRequestFactory;
use Messenger\Domain\Response\SendMessageResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\SendMessagePresenterTest;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\UseCase\SendMessage;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class SendMessageTest extends TestCase
{
    private SendMessagePresenterTest $presenter;
    private UserRepository $userGateway;
    private SendMessage $useCase;
    private SendMessageRequestFactory $requestFactory;

    protected function setUp(): void
    {
        $this->presenter = new SendMessagePresenterTest();
        $this->userGateway = new UserRepository();
        $discussionGateway = new DiscussionRepository();
        $this->useCase = new SendMessage(new MessageRepository(), $discussionGateway);
        $this->requestFactory = new SendMessageRequestFactory($discussionGateway);
    }

    public function testSuccessful(): void
    {
        $messageContent = "message content";
        $username = 'username1';
        $discussionId = "8dada3a9-f7fa-488c-9657-c4caa3fc2a35";
        $request = $this->requestFactory->create(
            $this->userGateway->findOneByUsername($username),
            $discussionId,
            $messageContent);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(SendMessageResponse::class, $this->presenter->response);

        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());
        $this->assertInstanceOf(Message::class, $this->presenter->response->getMessage());
        $this->assertEquals($discussionId, $this->presenter->response->getDiscussion()->getId()->toString());
        $this->assertInstanceOf(Uuid::class, $this->presenter->response->getMessage()->getId());
        $this->assertEquals((new \DateTime())->format('Y-m-d'), $this->presenter->response->getMessage()->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals($messageContent, $this->presenter->response->getMessage()->getMessage());
        $this->assertEquals($username, $this->presenter->response->getMessage()->getAuthor()->getUsername());
        $this->assertEquals($discussionId, $this->presenter->response->getMessage()->getDiscussion()->getId()->toString());
    }


    /**
     * @dataProvider provideFailedValidationRequestsData
     * @param string|null $messageContent
     * @param string $discussionId
     * @param string $username
     * @param string $exception
     * @return void
     * @throws AssertionFailedException
     * @throws NotAMemberOfTheDiscussionException
     * @throws SendMessageForbiddenException
     * @throws DiscussionNotFoundException
     */
    public function testFailedValidation(?string $messageContent, string $discussionId, string $username, string $exception): void
    {
        $this->expectException($exception);

        $this->requestFactory->create(
            $this->userGateway->findOneByUsername($username),
            $discussionId,
            $messageContent);
    }

    public function provideFailedValidationRequestsData(): \Generator
    {
        yield ["", "8dada3a9-f7fa-488c-9657-c4caa3fc2a35", 'username1', AssertionFailedException::class];
        yield ["", "8dada3a9-f7fa-488c-9657-c4caa3fc2a35", 'username2', NotAMemberOfTheDiscussionException::class];
    }

    public function testForbidden(): void
    {
        $this->expectException(SendMessageForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []),
            "8dada3a9-f7fa-488c-9657-c4caa3fc2a35", 'username1');
    }

    public function testNotFound(): void
    {
        $this->expectException(DiscussionNotFoundException::class);
        $this->requestFactory->create($this->userGateway->findOneByUsername('username1'),
            "8dada3a0-f7fa-488c-9657-c4caa3fc2a35", 'username1');
    }
}
