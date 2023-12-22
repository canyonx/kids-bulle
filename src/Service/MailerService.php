<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    // Service dans un service, Injection de dépendance
    private $mailer;
    private $adminMail;

    public function __construct(
        MailerInterface $mailer,
        ParameterBagInterface $parameterBag
    ) {
        $this->mailer = $mailer;
        $this->adminMail = $parameterBag->get('app.admin_mail');
    }

    /**
     * Envoyer un email
     *
     * @param string $subject of the mail
     * @param string $template link to template
     * @param array $context array variables for the template
     * @return void
     */
    public function send(string $to, string $subject, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->from($this->adminMail)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("email/$template.html.twig")
            ->context($context);

        $this->mailer->send($email);
    }
}
