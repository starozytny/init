<?php

namespace App\Controller\User;

use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/espace-utilisateur/agenda", name="user_agenda_")
 */
class AgendaController extends AbstractController
{
    const ATTRIBUTES_DATE = [];

    /**
     * @Route("/", name="index")
     */
    public function index(CalendarService $calendarService, SerializeData $serializer)
    {
        $week = $calendarService->getThisWeek();

        $week = $serializer->getSerializeData($week, self::ATTRIBUTES_DATE);

        dump($week);

        return $this->render('root/user/pages/agenda/index.html.twig', [
            'week' => $week
        ]);
    }
}
