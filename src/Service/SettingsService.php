<?php


namespace App\Service;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SettingsService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSettings(){
        $set = $this->em->getRepository(Settings::class)->findAll();
        if(count($set) == 0){
            throw new Exception('[Erreur MAIL] Les settings ne sont pas paramétrés.');
        }

        return $set[0];
    }

    public function getWebsiteName(){
        $setting = $this->getSettings();

        return $setting->getWebsiteName();
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
