<?php


use Messenger\Domain\Entity\Discussion;
use Messenger\Domain\Entity\Member;
use Messenger\Domain\Entity\Message;
use Messenger\Domain\Exception\DeleteMessageForbiddenException;
use Messenger\Domain\Exception\MessageNotFoundException;
use Messenger\Domain\RequestFactory\DeleteMessageRequestFactory;
use Messenger\Domain\Response\DeleteMessageResponse;
use Messenger\Domain\TestsIntegration\Adapter\Logger;
use Messenger\Domain\TestsIntegration\Adapter\Presenter\DeleteMessagePresenterTest;
use Messenger\Domain\TestsIntegration\Adapter\Repository\MessageRepository;
use Messenger\Domain\TestsIntegration\Adapter\Repository\UserRepository;
use Messenger\Domain\TestsIntegration\Entity\User;
use Messenger\Domain\UseCase\DeleteMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DeleteMessageTest extends TestCase
{
    private DeleteMessagePresenterTest $presenter;
    private UserRepository $userRepository;
    private DeleteMessage $useCase;
    private DeleteMessageRequestFactory $requestFactory;

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
            ],
            'messages' => []
        ];
        $data['discussions'] = [
            new Discussion(
                $data['members'][0],
                new Uuid("72b0b1a3-cf8c-4058-b526-4566acd49377"),
                "discussion 1")
        ];
        $data['discussions'][0]->addMember($data['members'][1]);
        $data['messages'][] = new Message(
            new Uuid('29771b87-5a17-4c4e-ab37-e815516dce73'),
            'message 1',
            $data['members'][0],
            new Uuid("72b0b1a3-cf8c-4058-b526-4566acd49377")
        );
        $messageRepository = new MessageRepository($data);
        $this->presenter = new DeleteMessagePresenterTest();
        $this->userRepository = new UserRepository($data);
        $this->useCase = new DeleteMessage(new MessageRepository($data), new Logger());
        $this->requestFactory = new DeleteMessageRequestFactory($messageRepository);
    }

    public function testSuccessful(): void
    {
        $request = $this->requestFactory->create(
            $this->userRepository->findOneByUsername('username1'),
            '29771b87-5a17-4c4e-ab37-e815516dce73');

        $this->useCase->execute($request, $this->presenter);

        $this->assertInstanceOf(DeleteMessageResponse::class, $this->presenter->response);

        $this->assertNull($this->presenter->response->getMessage()->getMessage());
        $this->assertTrue($this->presenter->response->getMessage()->isDeleted());
    }

    public function testFailedValidation(): void
    {
        $this->expectException(DeleteMessageForbiddenException::class);
        $this->requestFactory->create(
            $this->userRepository->findOneByUsername('username3'),
            "29771b87-5a17-4c4e-ab37-e815516dce73");
    }

    public function testNotFoundValidation(): void
    {
        $this->expectException(MessageNotFoundException::class);
        $this->requestFactory->create(
            $this->userRepository->findOneByUsername('username3'),
            "5142abe3-21e2-4363-ba31-d0271f94824e");
    }

    public function testForbidden(): void
    {
        $this->expectException(DeleteMessageForbiddenException::class);
        $this->requestFactory->create(new User('username+forbidden@email.com', 'username+forbidden', []),
            "29771b87-5a17-4c4e-ab37-e815516dce73");
    }
}
