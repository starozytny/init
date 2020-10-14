<?php

namespace App\Controller;

use App\Entity\Rgpd;
use App\Entity\Settings;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{
    /**
     * @Route("/administrator", options={"expose"=true}, name="super_dash")
     */
    public function indexRedirect()
    {
        return $this->redirectToRoute('super_dashboard');
    }
    /**
     * @Route("/administrator/dashboard", options={"expose"=true}, name="super_dashboard")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository(Settings::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $newUsers = $em->getRepository(User::class)->findBy(['is_new' => true]);
        $totalUsers = count($users);
        $totalNewUsers = count($newUsers);

        $rgpds = $em->getRepository(Rgpd::class)->findBy(['isSeen' => false, 'isTrash' => false]);
        $totalRgpds = count($rgpds);

        $haveSettings = true;
        if(count($settings) == 0){
            $haveSettings = false;
        }

        return $this->render('root/super/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalNewUsers' => $totalNewUsers,
            'totalRgpds' => $totalRgpds,
            'haveSettings' => $haveSettings
        ]);
    }
}
