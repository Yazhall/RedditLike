<?php


namespace App\Tests\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAccessTest extends WebTestCase
{
    public function testAdminPageRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseRedirects('/login');
    }

    public function testUserCannotAccessAdmin(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('user_test');
        $user->setFirstname('User');
        $user->setLastname('Test');
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('test');
        $user->setEnabled(true);

        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(403);
    }


    public function testAdminCanAccessAdmin(): void
    {
        $client = static::createClient();
        $em = self::getContainer()->get('doctrine')->getManager();

        $admin = new User();
        $admin->setUsername('admin_test');
        $admin->setFirstname('Admin');
        $admin->setLastname('Test');
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('test');
        $admin->setEnabled(true);

        $em->persist($admin);
        $em->flush();

        $client->loginUser($admin);
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }





}

