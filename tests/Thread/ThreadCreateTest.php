<?php

namespace App\Tests\Controller;

use App\Entity\Thread;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThreadCreateTest extends WebTestCase
{
    public function testUserCanCreateThread(): void
    {
        $client = static::createClient();
        $container = self::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);


        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => 'user@test.com']);

        $this->assertNotNull($user);


        $client->loginUser($user);


        $client->request('POST', '/creat-thread', [
            'thread' => [
                'title'   => 'Thread de test',
                'content' => 'Contenu du thread de test',
            ],
        ]);


        $this->assertResponseRedirects('/feed_thread');


        $thread = $em->getRepository(Thread::class)
            ->findOneBy(['title' => 'Thread de test']);

        $this->assertNotNull($thread);
        $this->assertSame('Contenu du thread de test', $thread->getContent());
        $this->assertSame(
            $user->getId()->toRfc4122(),
            $thread->getAuthor()->getId()->toRfc4122()
        );
    }
}
