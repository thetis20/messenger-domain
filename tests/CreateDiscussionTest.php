<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\UseCase\CreateDiscussion;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;

class CreateDiscussionTest extends TestCase
{
    private CreateDiscussionPresenterInterface $presenter;
    private UserRepository $userGateway;
    private CreateDiscussion $useCase;

    protected function setUp(): void
    {
        $this->presenter = new class() implements CreateDiscussionPresenterInterface {
            public CreateDiscussionResponse $response;

            public function present(CreateDiscussionResponse $response): void
            {
                $this->response = $response;
            }
        };
        $this->userGateway = new UserRepository();
        $this->useCase = new CreateDiscussion(new DiscussionRepository());
    }

    public function testSuccessful(): void
    {
        $request = CreateDiscussionRequest::create(
            "discussion name",
            [$this->userGateway->findOneByUsername('username1')],
            $this->userGateway->findOneByUsername('username'));

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(CreateDiscussionResponse::class, $this->presenter->response);

        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());
        $this->assertCount(2, $this->presenter->response->getDiscussion()->getMembers());
        $usernames = array_map(function ($member) {
            return $member->getUsername();
        }, $this->presenter->response->getDiscussion()->getMembers());
        $this->assertContains('username', $usernames);
        $this->assertContains('username1', $usernames);
    }


    /**
     * @dataProvider provideFailedValidationRequestsData
     * @param string $name
     * @param array<string> $users
     * @param string $currentUser
     * @return void
     */
    public function testFailedValidation(string $name, array $usernameMembers, ?string $usernameAuthor): void
    {
        $users = array_map(function ($username) {
            return $this->userGateway->findOneByUsername($username);
        }, $usernameMembers);
        $currentUser = $usernameAuthor ? $this->userGateway->findOneByUsername($usernameAuthor) : $usernameAuthor;
        $this->expectException(AssertionFailedException::class);
        CreateDiscussionRequest::create($name, $users, $currentUser);
    }

    public function provideFailedValidationRequestsData(): \Generator
    {
        yield ["", ['username1'], 'username'];
        yield ["name", [], 'username'];
        yield ["name", ['username-unknown'], 'username'];
        yield ["name", ['username'], 'username'];
    }
}
