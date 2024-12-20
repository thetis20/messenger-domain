<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
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
        /** @var array{users: User[], discussions:Discussion[], members: Member[], messages: Message[]} $data */
        $data = [
            'users' => [
                new User('username1@email.com', 'username1'),
                new User('username2@email.com', 'username2'),
                new User('username3@email.com', 'username3')
            ],
            'discussions' => [
                new Discussion(
                    new Uuid("45eb17ea-e3f5-414a-adbc-b807705ab3d9"),
                    "discussion 1")
            ],
            'members' => [
                new Member('username1@email.com', 'username1', 'username1'),
                new Member('username2@email.com', 'username2', 'username2'),
            ],
            'messages' => []
        ];
        $data['discussions'][0]->addMember($data['members'][0]);
        $data['discussions'][0]->addMember($data['members'][1]);
        $this->presenter = new SendMessagePresenterTest();
        $this->userGateway = new UserRepository($data);
        $discussionGateway = new DiscussionRepository($data);
        $this->useCase = new SendMessage(new MessageRepository($data), $discussionGateway);
        $this->requestFactory = new SendMessageRequestFactory($discussionGateway);
    }

    public function testSuccessful(): void
    {
        $messageContent = "message content";
        $username = 'username1';
        $discussionId = "45eb17ea-e3f5-414a-adbc-b807705ab3d9";
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
        $this->assertEquals($discussionId, $this->presenter->response->getMessage()->getDiscussionId());
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
        yield ["", "45eb17ea-e3f5-414a-adbc-b807705ab3d9", 'username1', AssertionFailedException::class];
        yield ["", "45eb17ea-e3f5-414a-adbc-b807705ab3d9", 'username3', NotAMemberOfTheDiscussionException::class];
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
