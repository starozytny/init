<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_contact;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_rgpd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_global;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(string $email_contact): self
    {
        $this->email_contact = $email_contact;

        return $this;
    }

    public function getEmailRgpd(): ?string
    {
        return $this->email_rgpd;
    }

    public function setEmailRgpd(string $email_rgpd): self
    {
        $this->email_rgpd = $email_rgpd;

        return $this;
    }

    public function getEmailGlobal(): ?string
    {
        return $this->email_global;
    }

    public function setEmailGlobal(string $email_global): self
    {
        $this->email_global = $email_global;

        return $this;
    }
}
