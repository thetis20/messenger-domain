<?php

namespace Messenger\Domain\RequestFactory;

use Assert\Assert;
use Messenger\Domain\Entity\UserInterface;
use Messenger\Domain\Exception\CreateDiscussionForbiddenException;
use Messenger\Domain\Request\CreateDiscussionRequest;

final class CreateDiscussionRequestFactory
{
    /**
     * @param string $name
     * @param string[] $emails
     * @param UserInterface $author
     * @return CreateDiscussionRequest
     * @throws CreateDiscussionForbiddenException
     */
    public function create(UserInterface $author, string $name, array $emails): CreateDiscussionRequest
    {
        if (!in_array('ROLE_USER', $author->getRoles())) {
            throw new CreateDiscussionForbiddenException($author);
        }

        Assert::lazy()
            ->that($name, 'name')->tryAll()->notEmpty()->string()
            ->that($author->getEmail(), 'author.email')->tryAll()->notInArray($emails)
            ->that($emails, 'emails')->tryAll()->minCount(1)->verifyNow();
        $assertion = Assert::lazy();
        foreach ($emails as $key => $email) {
            $assertion->that($email, 'emails.' . $key)->tryAll()
                ->string()->notEmpty()
                ->email();
        }
        $assertion->verifyNow();

        return new CreateDiscussionRequest($name, $emails, $author);
    }

}