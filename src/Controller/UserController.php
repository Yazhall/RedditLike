<?php
#mettre plus de commentaire
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\TokenRepository;
use App\Service\MailerService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use http\Encoding\Stream\Inflate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserController extends AbstractController
{
    #[Route('/register', name: 'app_user_create')]
    public function create(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        TokenService $tokenService,
        MailerService $mailer
    ): Response
    {
        $request->getSession()->invalidate();
        $user = new User();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # Récupération du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword(
                $passwordHasher->hashPassword($user, $plainPassword)
            );

            # Génération du token
            $token = $tokenService->createToken($user);
            $user->addToken($token);
            $entityManager->persist($token);
            $entityManager->persist($user);
            $entityManager->flush();

            # Envoi du mail via service
            $mailer->sendEmailValidation(
                $user->getEmail(),
                'Validation de votre compte',
                'Mail/email_validation',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('Page/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        return $this->render('Page/user/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/validate/{token}', name: 'app_validate')]
    public function ValidationMail(
        string $token,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager
    ): Response {


        $tokenEntity = $tokenRepository->findOneBy(['tokenValue' => $token]);

        if (!$tokenEntity) {
            throw $this->createNotFoundException('Token invalide.');
        }


        if ($tokenEntity->getExpiresAt() < new \DateTime()) {
            throw $this->createAccessDeniedException('Ce lien de validation a expiré.');
        }


        $user = $tokenEntity->getUser();
        $user->setIsVerified(true);
        $user->setEnabled(true);


        $entityManager->persist($user);
        $entityManager->remove($tokenEntity);
        $entityManager->flush();

        $this->addFlash('success', 'Compte validé ! Vous pouvez vous connecter.');

        return $this->redirectToRoute('app_login');
    }

}
