<?php

namespace App\Command;

use App\Entity\Contact;
use App\Service\CheckTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminRgpdExpireCommand extends Command
{
    protected static $defaultName = 'admin:rgpd:expire';
    protected $em;
    protected $checkTime;

    public function __construct(EntityManagerInterface $em, CheckTime $checkTime)
    {
        parent::__construct();

        $this->em = $em;
        $this->checkTime = $checkTime;
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete data if > 3 years related to GRPD.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $compteur = 0;
        $contacts = $this->em->getRepository(Contact::class)->findAll();
        foreach($contacts as $contact){
            $resultat = $this->checkTime->deleteMoreThreeYear($contact->getCreateAt(), $contact);
            $compteur = ($resultat == 1) ? $compteur + 1 : $compteur;
        }

        $io->text('Nombre de suppression : ' . $compteur);
        $io->comment('--- [FIN DE LA COMMANDE] ---');
    }
}
