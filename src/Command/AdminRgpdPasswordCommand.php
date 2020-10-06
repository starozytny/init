<?php

namespace App\Command;

use App\Entity\User;
use App\Service\CheckTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminRgpdPasswordCommand extends Command
{
    protected static $defaultName = 'admin:rgpd:password';
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
            ->setDescription('Check if an user has to renew password because > 5 years.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $compteur = 0;
        $users = $this->em->getRepository(User::class)->findAll();
        foreach($users as $user){
            $resultat = $this->checkTime->moreThanToday($user->getRenouvTime());
            if($resultat == 1){
                $compteur++;
                //send mail to informe use to renew his password
            }
        }

        $io->text('Nombre d\'utilisateurs concernés : ' . $compteur);
        $io->comment('--- [FIN DE LA COMMANDE] ---');

        return 0;
    }
}
