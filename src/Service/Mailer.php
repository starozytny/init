<?php


namespace App\Service;

use App\Entity\Settings;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;
    private $settingsService;

    public function __construct(MailerInterface $mailer, SettingsService $settingsService)
    {
        $this->mailer = $mailer;
        $this->settingsService = $settingsService;
    }

    public function sendMail($title, $text, $html, $params, $email, $from = null)
    {
        $from = ($from == null) ? $this->settingsService->getEmailExpediteurGlobal() : $from;

        $email = (new TemplatedEmail())
            ->from($from)
            ->cc($email)
            ->subject($title)
            ->text($text)
            ->htmlTemplate($html)
            ->context($params)
        ;

        if($this->mailer->send($email)){
            return true;
        } else {
            return 'Le message n\'a pas pu être délivré. Veuillez contacter le support.';
        }
    }
}
