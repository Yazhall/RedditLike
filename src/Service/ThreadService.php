<?php

namespace App\Service;

use App\Entity\Thread;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function create(Thread $thread, User $author): void
    {
        $thread->setAuthor($author);
        $this->entityManager->persist($thread);
        $this->entityManager->flush();
    }

    public function update(Thread $thread, UserInterface $user): void
    {
        $this->assertAuthor($thread, $user);
        $this->entityManager->flush();
    }

    public function softDelete(Thread $thread, UserInterface $user): void
    {
        $this->assertAuthor($thread, $user);
        $thread->setIsDeleted(true);
        $this->entityManager->flush();
    }

    private function assertAuthor(Thread $thread, UserInterface $user): void
    {
        if (
            !$user instanceof User ||
            $thread->getAuthor() !== $user
        ) {
            throw new AccessDeniedException('Vous ne pouvez pas modifier ce thread.');
        }
    }
}
