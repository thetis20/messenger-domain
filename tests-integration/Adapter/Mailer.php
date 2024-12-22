<?php

namespace Messenger\Domain\TestsIntegration\Adapter;

use Messenger\Domain\Gateway\NotificationGateway;

class Mailer extends NotificationGateway
{
    /** @var array<string|array<string|mixed>>[] */
    private array $sentNotifications = [];

    public function invitesDiscussion(string $email, array $params): void
    {
        $this->sentNotifications[] = ['invitesDiscussion', $email, $params];
    }

    public function invitesMemberDiscussion(string $email, array $params): void
    {
        $this->sentNotifications[] = ['invitesMemberDiscussion', $email, $params];
    }

    public function newMessage(string $email, array $params): void
    {
        $this->sentNotifications[] = ['newMessage', $email, $params];
    }

    /**
     * @return array<string|mixed>[]
     */
    public function getSentNotifications(): array
    {
        return $this->sentNotifications;
    }
}