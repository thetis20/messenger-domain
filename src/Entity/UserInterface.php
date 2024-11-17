<?php

namespace Messenger\Domain\Entity;

use Symfony\Component\Uid\Uuid;

interface UserInterface
{
    /**
     * @return Uuid
     */
    public function getId(): Uuid;

    /**
     * @return string
     */
    public function getUsername(): string;
 }
