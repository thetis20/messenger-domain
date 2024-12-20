<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\DiscussionNotFoundException;
use Messenger\Domain\Exception\DiscussionNotSetException;
use Messenger\Domain\Exception\SendMessageForbiddenException;
use Messenger\Domain\Exception\ShowDiscussionForbiddenException;
use Messenger\Domain\RequestFactory\ShowDiscussionRequestFactory;
use Messenger\Domain\Response\ShowDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\ShowDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\UseCase\ShowDiscussion;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShowDiscussionTest extends TestCase
{
    private ShowDiscussionPresenterTest $presenter;
    private UserRepository $userRepository;
    private ShowDiscussion $useCase;
    private ShowDiscussionRequestFactory $requestFactory;

    protected function setUp(): void
    {
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
        foreach ($data['discussions'] as $discussion) {
            $countMembers = count($discussion->getDiscussionMembers());
            for ($i = 1; $i <= 10; $i++) {
                $data['messages'][] = new Message(
                    Uuid::v4(),
                    'message ' . $i,
                    $discussion->getDiscussionMembers()[$i % $countMembers]->getMember(),
                    $discussion->getId()
                );
            }
        }
        $discussionRepository = new DiscussionRepository($data);
        $this->presenter = new ShowDiscussionPresenterTest();
        $this->userRepository = new UserRepository($data);
        $this->useCase = new ShowDiscussion(new MessageRepository($data));
        $this->requestFactory = new ShowDiscussionRequestFactory($discussionRepository);
    }

    /**
     * @dataProvider provideSuccessfulValidationRequestsData
     * @param string $username
     * @param string $discussionId
     * @param string $discussionName
     * @param int $limit
     * @param int $page
     * @param int $count
     * @param int $total
     * @param int $totalPage
     * @param bool $hasNextPage
     * @param bool $hasPreviousPage
     * @param int|null $nextPage
     * @param int|null $previousPage
     * @throws DiscussionNotFoundException
     * @throws ShowDiscussionForbiddenException
     */
    public function testSuccessful(
        string $username,
        string $discussionId,
        string $discussionName,
        int    $limit,
        int    $page,
        int    $count,
        int    $total,
        int    $totalPage,
        bool   $hasNextPage,
        bool   $hasPreviousPage,
        ?int   $nextPage,
        ?int   $previousPage): void
    {
        $request = $this->requestFactory->create(
            $this->userRepository->findOneByUsername($username),
            $discussionId,
            [
                'limit' => $limit,
                'page' => $page,
            ]);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(ShowDiscussionResponse::class, $this->presenter->response);

        $this->assertEquals($discussionName, $this->presenter->response->getDiscussion()->getName());
        $this->assertCount($count, $this->presenter->response->getMessages());
        $this->assertEquals($page, $this->presenter->response->getPage());
        $this->assertEquals($total, $this->presenter->response->getTotal());
        $this->assertEquals($totalPage, $this->presenter->response->getTotalPages());
        $this->assertEquals($hasNextPage, $this->presenter->response->hasNextPage());
        $this->assertEquals($hasPreviousPage, $this->presenter->response->hasPreviousPage());
        $this->assertEquals($nextPage, $this->presenter->response->getNextPage());
        $this->assertEquals($previousPage, $this->presenter->response->getPreviousPage());
        $this->assertEquals($limit, $this->presenter->response->getLimit());
    }

    public function provideSuccessfulValidationRequestsData(): \Generator
    {
        yield ['username1', "45eb17ea-e3f5-414a-adbc-b807705ab3d9", "discussion 1", 10, 1, 10, 10, 1, false, false, null, null];
        yield ['username1', "45eb17ea-e3f5-414a-adbc-b807705ab3d9", "discussion 1", 3, 1, 3, 10, 4, true, false, 2, null];
        yield ['username1', "45eb17ea-e3f5-414a-adbc-b807705ab3d9", "discussion 1", 3, 3, 3, 10, 4, true, true, 4, 2];
        yield ['username1', "45eb17ea-e3f5-414a-adbc-b807705ab3d9", "discussion 1", 3, 4, 1, 10, 4, false, true, null, 3];
    }

    public function testFailedValidation(): void
    {
        $this->expectException(ShowDiscussionForbiddenException::class);
        $this->requestFactory->create(
            $this->userRepository->findOneByUsername('username3'),
            "45eb17ea-e3f5-414a-adbc-b807705ab3d9");
    }

    public function testNotFoundValidation(): void
    {
        $this->expectException(DiscussionNotFoundException::class);
        $this->requestFactory->create(
            $this->userRepository->findOneByUsername('username3'),
            "5142abe3-21e2-4363-ba31-d0271f94824e");
    }

    public function testForbidden(): void
    {
        $this->expectException(ShowDiscussionForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []),
            "8dada3a9-f7fa-488c-9657-c4caa3fc2a35");
    }
}
