<?php


use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\MarkAsDiscussionForbiddenException;
use Messenger\Domain\Exception\NotAMemberOfTheDiscussionException;
use Messenger\Domain\Exception\SendMessageForbiddenException;
use Messenger\Domain\RequestFactory\MarkAsDiscussionRequestFactory;
use Messenger\Domain\RequestFactory\SendMessageRequestFactory;
use Messenger\Domain\Response\MarkAsDiscussionResponse;
use Messenger\Domain\Response\SendMessageResponse;
use Messenger\Domain\TestsIntegration\Adapter\Logger;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\MarkAsDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\SendMessagePresenterTest;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\UseCase\MarkAsDiscussion;
use Messenger\Domain\UseCase\SendMessage;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class MarkAsDiscussionTest extends TestCase
{
    private UserRepository $userGateway;
    private MarkAsDiscussion $useCase;
    private MarkAsDiscussionRequestFactory $requestFactory;

    protected function setUp(): void
    {
        $data = [
            'users' => [
                new User('username1@email.com', 'username1'),
                new User('username2@email.com', 'username2'),
                new User('username3@email.com', 'username3')
                ],
            'members' => [
                new Member('username1@email.com', 'username1', 'username1'),
                new Member('username2@email.com', 'username2', 'username2'),
            ]
        ];
        $data['discussions'] = [
            new Discussion(
                $data['members'][0],
                new Uuid("45eb17ea-e3f5-414a-adbc-b807705ab3d9"),
                "discussion 1")
        ];
        $data['discussions'][0]->addMember($data['members'][1]);
        $this->userGateway = new UserRepository($data);
        $discussionGateway = new DiscussionRepository($data);
        $this->useCase = new MarkAsDiscussion($discussionGateway, new Logger());
        $this->requestFactory = new MarkAsDiscussionRequestFactory($discussionGateway);
    }

    public function testSuccessful(): void
    {
        $username = 'username1';
        $discussionId = "45eb17ea-e3f5-414a-adbc-b807705ab3d9";
        $presenter = new MarkAsDiscussionPresenterTest();
        $request = $this->requestFactory->create(
            $this->userGateway->findOneByUsername($username),
            $discussionId,
            false);

        $this->useCase->execute($request, $presenter);

        $this->assertInstanceOf(MarkAsDiscussionResponse::class, $presenter->response);
        $discussionMember = $presenter->response->getDiscussion()->findDiscussionMemberByEmail('username1@email.com');
        $this->assertEquals(false, $discussionMember->isSeen());

        $presenter = new MarkAsDiscussionPresenterTest();
        $request = $this->requestFactory->create(
            $this->userGateway->findOneByUsername($username),
            $discussionId);

        $this->useCase->execute($request, $presenter);

        $this->assertInstanceOf(MarkAsDiscussionResponse::class, $presenter->response);
        $discussionMember = $presenter->response->getDiscussion()->findDiscussionMemberByEmail('username1@email.com');
        $this->assertEquals(true, $discussionMember->isSeen());
    }


    /**
     * @return void
     * @throws DiscussionNotFoundException
     * @throws MarkAsDiscussionForbiddenException
     * @throws NotAMemberOfTheDiscussionException
     */
    public function testFailedValidation(): void
    {
        $this->expectException(NotAMemberOfTheDiscussionException::class);

        $this->requestFactory->create(
            $this->userGateway->findOneByUsername('username3'),
            "45eb17ea-e3f5-414a-adbc-b807705ab3d9");
    }

    public function testForbidden(): void
    {
        $this->expectException(MarkAsDiscussionForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []),
            "45eb17ea-e3f5-414a-adbc-b807705ab3d9");
    }

    public function testNotFound(): void
    {
        $this->expectException(DiscussionNotFoundException::class);
        $this->requestFactory->create($this->userGateway->findOneByUsername('username1'),
            "8dada3a0-f7fa-488c-9657-c4caa3fc2a35");
    }
}
