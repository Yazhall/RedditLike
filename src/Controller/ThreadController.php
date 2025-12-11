<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Form\Type\ThreadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThreadController extends AbstractController
{

    #[Route('/creat-thread', name: 'app_thread_create')]
    public function createFeed(
        Request $request,
        EntityManagerInterface $entityManager,
    ):Response{
        $thread = new Thread();


        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $thread->setAuthor($this->getUser());

            $entityManager->persist($thread);
            $entityManager->flush();
            return $this->redirectToRoute('app_feed_thread');
        }

        return $this->render('Page/thread/creat_thead.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/feed_thread', name: 'app_feed_thread')]
    public function FeedList(
        Request $request,
        EntityManagerInterface $entityManager,
    ):Response
    {
        $threads = $entityManager->getRepository(Thread::class)->findAll(['createdAt' => 'DESC']);
        return $this->render('Page/thread/feed_thread.html.twig', ['threads' => $threads,]);
    }
}
