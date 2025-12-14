<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Thread;
use App\Repository\ThreadRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(

    ): Response
    {
        return $this->render('Page/admin/admin_dashboard.html.twig');
    }

    #[Route('/users', name: 'app_admin_users')]
    public function userList(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('Page/admin/admin_users_list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users/{id}/toggle-ban', name: 'app_admin_user_toggle_ban', methods: ['POST'])]
    public function toggleBan(
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user->setEnabled(!$user->isEnabled());
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/users/{id}/toggle-admin', name: 'app_admin_user_toggle_admin', methods: ['POST'])]
    public function toggleAdmin(
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $roles = $user->getRoles();
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Tu ne peux pas retirer ton propre rôle admin.');
            return $this->redirectToRoute('app_admin_users');
        }

        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
        } else {
            $roles[] = 'ROLE_ADMIN';
        }

        $user->setRoles(array_unique($roles));
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users');
    }
    #[Route('/threads', name: 'app_admin_threads')]
    public function threadList(
        ThreadRepository $threadRepository
    ): Response
    {
        return $this->render('Page/admin/admin_threads.html.twig', [
            'threads' => $threadRepository->findAllWithDeleted(),
        ]);
    }

    #[Route('/threads/{id}/delete', name: 'app_admin_thread_delete', methods: ['POST'])]
    public function deleteThread(
        Thread $thread,
        Request $request,
        EntityManagerInterface $entityManager

    ): Response {
        if ($this->isCsrfTokenValid('admin_delete_thread_'.$thread->getId(), $request->request->get('_token'))) {
            $thread->setIsDeleted(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_threads');
    }
    #[Route('/thread/{id}/restore', name: 'app_admin_thread_restore', methods: ['POST'])]
    public function restoreThread(
        Thread $thread,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('admin_restore_thread_'.$thread->getId(),
            $request->request->get('_token')
        )) {
            $thread->setIsDeleted(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_threads');
    }


}
