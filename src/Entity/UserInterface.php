<?php

namespace Messenger\Domain\Entity;

interface UserInterface
{
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored in a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[]
     */
    public function getRoles(): array;

    /**
     * Returns the identifier for this user (e.g. username or email address).
     *
     * @return non-empty-string
     */
    public function getUserIdentifier(): string;

    /**
     * Returns the email for this user.
     *
     * @return non-empty-string
     */
    public function getEmail(): string;

    /**
     * Returns the usual name for this user.
     *
     * @return non-empty-string
     */
    public function getUsualName(): string;


}