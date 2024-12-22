<?php

namespace Messenger\Domain\Tests;

use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;
use Messenger\Domain\RequestFactory\CreateDiscussionRequestFactory;
use Messenger\Domain\Response\CreateDiscussionResponse;
use Messenger\Domain\TestsIntegration\Adapter\Mailer;
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
    private Mailer $mailer;

    protected function setUp(): void
    {
        $data = [
            'users' => [new User('username@email.com', 'username')],
            'discussions' => [],
            'members' => [
                new Member('username@email.com', 'username', 'username'),
                new Member('username1@email.com', 'username1', 'username1'),
            ],
        ];
        $this->mailer = new Mailer();
        $this->presenter = new CreateDiscussionPresenterTest();
        $this->userGateway = new UserRepository($data);
        $this->useCase = new CreateDiscussion(new DiscussionRepository($data), new MemberRepository($data), $this->mailer);
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
            ['username1@email.com', 'username2@email.com']);

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(CreateDiscussionResponse::class, $this->presenter->response);
        $this->assertInstanceOf(Discussion::class, $this->presenter->response->getDiscussion());

        $discussionMembers = $this->presenter->response->getDiscussion()->getDiscussionMembers();

        $this->assertCount(3, $discussionMembers);
        $this->assertEquals('username@email.com', $discussionMembers[0]->getMember()->getEmail());
        $this->assertEquals('username', $discussionMembers[0]->getMember()->getUserIdentifier());
        $this->assertEquals('username', $discussionMembers[0]->getMember()->getUsername());
        $this->assertTrue($discussionMembers[0]->isSeen());
        $this->assertEquals('username1@email.com', $discussionMembers[1]->getMember()->getEmail());
        $this->assertEquals('username1', $discussionMembers[1]->getMember()->getUserIdentifier());
        $this->assertEquals('username1', $discussionMembers[1]->getMember()->getUsername());
        $this->assertEquals($this->presenter->response->getDiscussion(), $discussionMembers[1]->getDiscussion());
        $this->assertFalse($discussionMembers[1]->isSeen());
        $this->assertEquals('username2@email.com', $discussionMembers[2]->getMember()->getEmail());
        $this->assertNull( $discussionMembers[2]->getMember()->getUserIdentifier());
        $this->assertNull($discussionMembers[2]->getMember()->getUsername());
        $this->assertEquals($this->presenter->response->getDiscussion(), $discussionMembers[2]->getDiscussion());
        $this->assertFalse($discussionMembers[2]->isSeen());

        // mailer
        $this->assertCount(2, $this->mailer->getSentNotifications());
        $this->assertEquals('invitesMemberDiscussion', $this->mailer->getSentNotifications()[0][0]);
        $this->assertEquals('username1@email.com', $this->mailer->getSentNotifications()[0][1]);
        $this->assertEquals($this->presenter->response->getDiscussion(), $this->mailer->getSentNotifications()[0][2]['discussion']);
        $this->assertEquals($discussionMembers[1]->getMember(), $this->mailer->getSentNotifications()[0][2]['member']);
        $this->assertEquals('invitesDiscussion', $this->mailer->getSentNotifications()[1][0]);
        $this->assertEquals('username2@email.com', $this->mailer->getSentNotifications()[1][1]);
        $this->assertEquals($this->presenter->response->getDiscussion(), $this->mailer->getSentNotifications()[1][2]['discussion']);
        $this->assertEquals($discussionMembers[2]->getMember(), $this->mailer->getSentNotifications()[1][2]['member']);
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
