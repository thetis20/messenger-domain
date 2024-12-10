<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;
use Messenger\Domain\Presenter\CreateDiscussionPresenterInterface;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\CreateDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MemberRepository;
use Messenger\Domain\UseCase\CreateDiscussion;
use Messenger\Domain\TestsIntegration\Adapter\Repository\DiscussionRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;

class CreateDiscussionTest extends TestCase
{
    private CreateDiscussionPresenterTest $presenter;
    private UserRepository $userGateway;
    private CreateDiscussion $useCase;

    protected function setUp(): void
    {
        $this->presenter = new CreateDiscussionPresenterTest();
        $this->userGateway = new UserRepository();
        $this->useCase = new CreateDiscussion(new DiscussionRepository(), new MemberRepository());
    }

    public function testSuccessful(): void
    {
        $request = CreateDiscussionRequest::create(
            "discussion name",
            ['username1@email.com'],
            $this->userGateway->findOneByUsername('username'));

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(CreateDiscussionResponse::class, $this->presenter->response);
        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());

        $discussionMembers = $this->presenter->response->getDiscussion()->getDiscussionMembers();

        $this->assertCount(2, $discussionMembers);
        $this->assertEquals('username@email.com', $discussionMembers[0]->getMember()->getEmail());
        $this->assertEquals('username', $discussionMembers[0]->getMember()->getUsername());
        $this->assertEquals('username1@email.com', $discussionMembers[1]->getMember()->getEmail());
        $this->assertEquals('username1', $discussionMembers[1]->getMember()->getUsername());
    }


    /**
     * @dataProvider provideFailedValidationRequestsData
     * @param string $name
     * @param string[] $members
     * @param string|null $usernameAuthor
     * @return void
     * @throws CreateDiscussionForbiddenException
     */
    public function testFailedValidation(string $name, array $members, ?string $usernameAuthor): void
    {
        $currentUser = $usernameAuthor ? $this->userGateway->findOneByUsername($usernameAuthor) : $usernameAuthor;
        $this->expectException(AssertionFailedException::class);
        CreateDiscussionRequest::create($name, $members, $currentUser);
    }

    public function provideFailedValidationRequestsData(): \Generator
    {
        yield ["", ['username1@email.com'], 'username'];
        yield ["name", [], 'username'];
        yield ["name", ['not-an-email'], 'username'];
        yield ["name", ['username@email.com'], 'username'];
    }
}
