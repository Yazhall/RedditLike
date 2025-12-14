<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Thread;
use App\Entity\ThreadFile;
use App\Form\Type\CommentType;
use App\Form\Type\ThreadType;
use App\Service\ThreadFileService;
use App\Service\ThreadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ThreadController extends AbstractController
{
    #[Route('/creat-thread', name: 'app_thread_create')]
    #[IsGranted('ROLE_USER')]
    public function createFeed(
        Request $request,
        EntityManagerInterface $entityManager,
        ThreadFileService $threadFileService,
    ): Response {

        $thread = new Thread();
        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $thread->setAuthor($this->getUser());
            $entityManager->persist($thread);

            $uploads = $form->get('uploads')->getData();
            if ($uploads) {
                $threadFileService->attachUploadsToThread($thread, $uploads);
            }

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
        $threads = $entityManager->getRepository(Thread::class)->findNotDeleted();
        $commentForms = [];
        foreach ($threads as $thread) {
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment, [
                'action' => $this->generateUrl('comment_add', ['id' => $thread->getId()])
            ]);
            $commentForms[$thread->getId()->toRfc4122()] = $form->createView();
        }
        return $this->render('Page/thread/feed_thread.html.twig', [
            'threads'      => $threads,
            'commentForms' => $commentForms,]);
    }
    #[Route('/thread/{id}', name: 'app_thread_show')]
    #[IsGranted('ROLE_USER')]
    public function show(
        Thread $thread,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setThread($thread);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_thread_show', [
                'id' => $thread->getId()->toRfc4122(),
            ]);
        }

        return $this->render('Page/thread/show_all_comment_on_thread.html.twig', [
            'thread' => $thread,
            'commentForm' => $form->createView(),
        ]);
    }
    #[Route('/thread/{id}/edit', name: 'app_thread_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Thread $thread,
        Request $request,
        ThreadService $threadService,
        ThreadFileService $threadFileService,

    ): Response {

        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $uploads = $form->get('uploads')->getData();
            if ($uploads) {
                $threadFileService->attachUploadsToThread($thread, $uploads);
            }

            $threadService->update($thread, $this->getUser());

            return $this->redirectToRoute('app_thread_show', [
                'id' => $thread->getId()->toRfc4122(),
            ]);
        }

        return $this->render('Page/thread/edit_thread.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread,
        ]);
    }
    #[Route('/thread/{id}/delete', name: 'app_thread_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(
        Thread $thread,
        Request $request,
        ThreadService $threadService,
    ): Response {

        if ($this->isCsrfTokenValid('delete_thread_'.$thread->getId(), $request->request->get('_token'))) {
            $threadService->softDelete($thread, $this->getUser());
        }

        return $this->redirectToRoute('app_my_thread_liste');
    }
    #[Route('/my_thread_liste', name: 'app_my_thread_liste')]
    public function showMyThreadListe(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $threads = $entityManager->getRepository(Thread::class)->findBy([
            'author' => $this->getUser(),
            'isDeleted' => false],
            ['createdAt' => 'DESC']);
        return $this->render('Page/thread/my_thread_list.html.twig', [
            'threads' => $threads,
        ]);

    }




}
