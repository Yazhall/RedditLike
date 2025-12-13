<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Thread;
use App\Form\Type\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment/add/{id}', name: 'comment_add', methods: ['POST'])]
    public function addComment(
        Thread $thread,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setAuthor($this->getUser());
            $comment->setThread($thread);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute('app_feed_thread');
        }


        return $this->redirectToRoute('app_feed_thread');
    }
}
