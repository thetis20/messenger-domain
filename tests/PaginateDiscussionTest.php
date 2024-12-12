<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Request\PaginateDiscussionRequest;
use Messenger\Domain\Response\PaginateDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\PaginateDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Messenger\Domain\UseCase\PaginateDiscussion;
use PHPUnit\Framework\TestCase;

class PaginateDiscussionTest extends TestCase
{
    private PaginateDiscussionPresenterTest $presenter;
    private UserRepository $userRepository;
    private PaginateDiscussion $useCase;

    protected function setUp(): void
    {
        $this->presenter = new PaginateDiscussionPresenterTest();
        $this->userRepository = new UserRepository();
        $this->useCase = new PaginateDiscussion(new DiscussionRepository());
    }

    /**
     * @dataProvider provideSuccessfulValidationRequestsData
     * @throws \Messenger\Domain\Exception\PaginateDiscussionForbiddenException
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
        $request = PaginateDiscussionRequest::create($this->userRepository->findOneByUsername($username),
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
}
