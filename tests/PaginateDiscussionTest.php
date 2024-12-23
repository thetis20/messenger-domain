<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Exception\PaginateDiscussionForbiddenException;
use Messenger\Domain\RequestFactory\PaginateDiscussionRequestFactory;
use Messenger\Domain\Response\PaginateDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\PaginateDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\UseCase\PaginateDiscussion;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PaginateDiscussionTest extends TestCase
{
    private PaginateDiscussionPresenterTest $presenter;
    private UserRepository $userRepository;
    private PaginateDiscussion $useCase;
    private PaginateDiscussionRequestFactory $requestFactory;

    /**
     * @return array{users: User[], discussions: Discussion[], members: Member[]}
     */
    private function getData(): array
    {
        $data = [
            'users' => [],
            'discussions' => [],
            'members' => []
        ];

        for ($i = 1; $i <= 10; $i++) {
            $data['users'][] = new User("username$i@email.com", "username$i");
        }

        for ($i = 1; $i <= 10; $i++) {
            $data['members'][] = new Member("username$i@email.com", "username$i", "username$i");
        }

        foreach ($data['members'] as $i => $m1) {
            foreach ($data['members'] as $y => $m2) {
                if ($i > $y) {
                    continue;
                }
                $discussion = new Discussion($m1, Uuid::v4(), "discussion $i/$y");
                $discussion->addMember($m2);
                $data['discussions'][] = $discussion;
            }
        }
        return $data;
    }

    protected function setUp(): void
    {
        $data = $this->getData();
        $this->presenter = new PaginateDiscussionPresenterTest();
        $this->userRepository = new UserRepository($data);
        $this->useCase = new PaginateDiscussion(new DiscussionRepository($data));
        $this->requestFactory = new PaginateDiscussionRequestFactory();
    }

    /**
     * @dataProvider provideSuccessfulValidationRequestsData
     * @throws PaginateDiscussionForbiddenException
     */
    public function testSuccessful(
        string $username,
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
            [
                'limit' => $limit,
                'page' => $page,
            ]);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(PaginateDiscussionResponse::class, $this->presenter->response);

        $this->assertCount($count, $this->presenter->response->getDiscussions());
        $this->assertEquals($page, $this->presenter->response->getPage());
        $this->assertEquals($total, $this->presenter->response->getTotal());
        $this->assertEquals($totalPage, $this->presenter->response->getTotalPages());
        $this->assertEquals($hasNextPage, $this->presenter->response->hasNextPage());
        $this->assertEquals($hasPreviousPage, $this->presenter->response->hasPreviousPage());
        $this->assertEquals($nextPage, $this->presenter->response->getNextPage());
        $this->assertEquals($previousPage, $this->presenter->response->getPreviousPage());
        $this->assertEquals($limit, $this->presenter->response->getLimit());
    }

    static public function provideSuccessfulValidationRequestsData(): \Generator
    {
        yield ['username2', 10, 1, 10, 10, 1, false, false, null, null];
        yield ['username2', 3, 1, 3, 10, 4, true, false, 2, null];
        yield ['username2', 3, 3, 3, 10, 4, true, true, 4, 2];
        yield ['username2', 3, 4, 1, 10, 4, false, true, null, 3];
    }

    public function testForbidden(): void
    {
        $this->expectException(PaginateDiscussionForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []));
    }
}
