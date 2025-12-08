<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    public function sendEmailValidation(
        string $to,
        string $subject,
        string $template,
        array $context = []
    ): void
    {
        $html = $this->twig->render($template . '.html.twig', $context);

        $email = (new Email())
            ->from('redditLike_validation_account@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);
    }
}
