<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index')]
    public function index(
        EntityManagerInterface $entityManager,
    ): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        return $this->render('Page/home/index.html.twig');
    }

    #[Route('/creat_user', name: 'app_create_user')]
    public function createUser(EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_home_index');
    }
}
