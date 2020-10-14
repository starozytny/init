<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AdminCreateSettingsCommand extends Command
{
    protected static $defaultName = 'admin:create:settings';

    protected function configure()
    {
        $this
            ->setDescription('Initiate settings website')
            ->addArgument('email_global', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('email_contact', InputArgument::REQUIRED, 'Argument description')
            ->addArgument('email_rgpd', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email_global = $input->getArgument('email_global');
        $email_contact = $input->getArgument('email_contact');
        $email_rgpd = $input->getArgument('email_rgpd');

        if(!$email_global || !$email_contact || !$email_rgpd){
            $io->error('Les arguments email_global, email_contact et email_rgpd sont obligatoires.');
        }

        $io->comment('--- [FIN DE LA COMMANDE] ---');

        return 0;
    }
}
