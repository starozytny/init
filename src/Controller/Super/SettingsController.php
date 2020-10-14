<?php

namespace App\Controller\Super;

use App\Entity\Settings;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administrator/parametres", name="super_settings_")
 */
class SettingsController extends AbstractController
{
    const ATTRIBUTES_SETTINGS = ['id', 'emailGlobal', 'emailContact', 'emailRgpd'];

    /**
     * @Route("/", options={"expose"=true}, name="edit")
     */
    public function edit(Request $request, SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository(Settings::class)->findAll();

        if($request->isMethod('POST')){
            $settings = (count($settings) == 0) ? new Settings() : $settings[0];

            $data = json_decode($request->getContent());
            $settings->setEmailGlobal($data->emailGlobal->value);
            $settings->setEmailContact($data->emailContact->value);
            $settings->setEmailRgpd($data->emailRgpd->value);
    
            $em->persist($settings); $em->flush();
            return new JsonResponse(['code' => 1]);
        }

        if(count($settings) == 0){
            return $this->render('root/super/pages/settings/edit.html.twig');
        }

        $settings = $serializer->getSerializeData($settings, self::ATTRIBUTES_SETTINGS);

        return $this->render('root/super/pages/settings/edit.html.twig', [
            'settings' => $settings
        ]);
    }
}
