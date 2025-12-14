<?php

namespace App\DataFixtures;

use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ThreadFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user_user', User::class);

        /** @var User $admin */
        $admin = $this->getReference('user_admin', User::class);

        $thread1 = new Thread();
        $thread1->setAuthor($user);
        $thread1->setTitle('Thread user');
        $thread1->setContent('Contenu du thread user');
        $manager->persist($thread1);

        $thread2 = new Thread();
        $thread2->setAuthor($admin);
        $thread2->setTitle('Thread admin');
        $thread2->setContent('Contenu du thread admin');
        $manager->persist($thread2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
