<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Exception\ShowDiscussionForbiddenException;
use Messenger\Domain\Request\ShowDiscussionRequest;
use Messenger\Domain\Response\ShowDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\ShowDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Messenger\Domain\UseCase\ShowDiscussion;
use PHPUnit\Framework\TestCase;

class ShowDiscussionTest extends TestCase
{
    private ShowDiscussionPresenterTest $presenter;
    private UserRepository $userRepository;
    private ShowDiscussion $useCase;
    private DiscussionRepository $discussionRepository;

    protected function setUp(): void
    {
        $this->presenter = new ShowDiscussionPresenterTest();
        $this->userRepository = new UserRepository();
        $this->discussionRepository = new DiscussionRepository();
        $this->useCase = new ShowDiscussion(new MessageRepository());
    }

    /**
     * @dataProvider provideSuccessfulValidationRequestsData
     * @param string $username
     * @param string $discussionId
     * @param int $limit
     * @param int $page
     * @param int $count
     * @param int $total
     * @param int $totalPage
     * @param bool $hasNextPage
     * @param bool $hasPreviousPage
     * @param int|null $nextPage
     * @param int|null $previousPage
     * @throws ShowDiscussionForbiddenException
     */
    public function testSuccessful(
        string $username,
        string $discussionId,
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
        $request = ShowDiscussionRequest::create(
            $this->userRepository->findOneByUsername($username),
            $this->discussionRepository->findOneById($discussionId),
            [
                'limit' => $limit,
                'page' => $page,
            ]);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(ShowDiscussionResponse::class, $this->presenter->response);

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
        yield ['username1', "5142abe2-21e2-4363-ba31-d0271f94824e", 10, 1, 10, 10, 1, false, false, null, null];
        yield ['username1', "5142abe2-21e2-4363-ba31-d0271f94824e", 3, 1, 3, 10, 4, true, false, 2, null];
        yield ['username1', "5142abe2-21e2-4363-ba31-d0271f94824e", 3, 3, 3, 10, 4, true, true, 4, 2];
        yield ['username1', "5142abe2-21e2-4363-ba31-d0271f94824e", 3, 4, 1, 10, 4, false, true, null, 3];
    }

    public function testFailedValidation(): void
    {
        $this->expectException(ShowDiscussionForbiddenException::class);
        ShowDiscussionRequest::create(
            $this->userRepository->findOneByUsername('username10'),
            $this->discussionRepository->findOneById("5142abe2-21e2-4363-ba31-d0271f94824e"));
    }
}
