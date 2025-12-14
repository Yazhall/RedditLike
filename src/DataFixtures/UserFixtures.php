<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setEmail('user@test.com');
        $user->setUsername('user');
        $user->setFirstname('User');
        $user->setLastname('Test');
        $user->setEnabled(true);
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_USER']);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );

        $manager->persist($user);
        $this->addReference('user_user', $user);


        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setUsername('admin');
        $admin->setFirstname('Admin');
        $admin->setLastname('Test');
        $admin->setEnabled(true);
        $admin->setIsVerified(true);
        $admin->setRoles(['ROLE_ADMIN']);

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin')
        );

        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        $manager->flush();
    }
}
