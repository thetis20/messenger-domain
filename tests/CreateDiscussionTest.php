<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;
use Messenger\Domain\Exception\ShowDiscussionForbiddenException;
use Messenger\Domain\Request\CreateDiscussionRequest;
use Messenger\Domain\RequestFactory\CreateDiscussionRequestFactory;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\CreateDiscussionPresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MemberRepository;
use Messenger\Domain\TestsIntegration\Entity\User;
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
    private CreateDiscussionRequestFactory $requestFactory;

    protected function setUp(): void
    {
        $this->presenter = new CreateDiscussionPresenterTest();
        $this->userGateway = new UserRepository();
        $this->useCase = new CreateDiscussion(new DiscussionRepository(), new MemberRepository());
        $this->requestFactory = new CreateDiscussionRequestFactory();
    }

    /**
     * @throws CreateDiscussionForbiddenException
     */
    public function testSuccessful(): void
    {
        $request = $this->requestFactory->create(
            $this->userGateway->findOneByUsername('username'),
            "discussion name",
            ['username1@email.com']);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(CreateDiscussionResponse::class, $this->presenter->response);
        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());

        $discussionMembers = $this->presenter->response->getDiscussion()->getDiscussionMembers();

        $this->assertCount(2, $discussionMembers);
        $this->assertEquals('username@email.com', $discussionMembers[0]->getMember()->getEmail());
        $this->assertEquals('username', $discussionMembers[0]->getMember()->getUserIdentifier());
        $this->assertEquals('username', $discussionMembers[0]->getMember()->getUsername());
        $this->assertEquals(true, $discussionMembers[0]->isSeen());
        $this->assertEquals('username1@email.com', $discussionMembers[1]->getMember()->getEmail());
        $this->assertEquals('username1', $discussionMembers[1]->getMember()->getUserIdentifier());
        $this->assertEquals('username1', $discussionMembers[1]->getMember()->getUsername());
        $this->assertEquals($this->presenter->response->getDiscussion(), $discussionMembers[1]->getDiscussion());
        $this->assertEquals(false, $discussionMembers[1]->isSeen());
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
        $this->requestFactory->create($currentUser, $name, $members);
    }

    static public function provideFailedValidationRequestsData(): \Generator
    {
        yield ["", ['username1@email.com'], 'username'];
        yield ["name", [], 'username'];
        yield ["name", ['not-an-email'], 'username'];
        yield ["name", ['username@email.com'], 'username'];
    }

    public function testForbidden(): void
    {
        $this->expectException(CreateDiscussionForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []),
            "name", ['username@email.com']);
    }

    public function testAuthorNotExistsInMembers(): void
    {
        $request = $this->requestFactory->create(
            new User('username+member@email.com', 'username+member'),
            "discussion member + name",
            ['username+member2@email.com']);

        $this->useCase->execute($request, $this->presenter);

        $discussionMembers = $this->presenter->response->getDiscussion()->getDiscussionMembers();
        $this->assertEquals('username+member@email.com', $discussionMembers[0]->getMember()->getEmail());
    }
}
