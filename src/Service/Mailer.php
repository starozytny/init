<?php


namespace App\Service;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;
    private $em;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function sendMail($title, $text, $html, $params, $email, $from = null)
    {
        $from = ($from == null) ? $this->getEmailExpediteurGlobal() : $from;

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

    private function getSettings(){
        $set = $this->em->getRepository(Settings::class)->findAll();
        if(count($set) == 0){
            throw new Exception('[Erreur MAIL] Les settings ne sont pas paramétrés.');
        }

        return $set[0];
    }

    public function getEmailExpediteurGlobal(){
        $setting = $this->getSettings();

        return $setting->getEmailGlobal();
    }

    public function getEmailContact(){
        $setting = $this->getSettings();

        return $setting->getEmailContact();
    }

    public function getEmailRgpd(){
        $setting = $this->getSettings();

        return $setting->getEmailRgpd();
    }
}
