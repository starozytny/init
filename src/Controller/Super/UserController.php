<?php

namespace App\Controller\Super;

use App\Entity\User;
use App\Service\Export;
use App\Service\FileUploader;
use App\Service\Mailer;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/administrator/utilisateurs", name="super_users_")
 */
class UserController extends AbstractController
{
    const ATTRIBUTES_USERS = ['id', 'username', 'roles', 'email', 'isNew', 'avatar', 'highRole', 'highRoleCode', 'createAtString', 'renouvTimeString', 'lastLoginString'];

    /**
     * @Route("/", options={"expose"=true}, name="index")
     */
    public function index(SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findBy([], ['username' => 'ASC']);

        $users = $serializer->getSerializeData($users, self::ATTRIBUTES_USERS);
        
        return $this->render('root/super/pages/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/update/utilisateur/{user}", options={"expose"=true}, name="user_update")
     */
    public function update(Request $request, $user, FileUploader $fileUploader)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user);
        if(!$user){
            return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur n\'existe pas.']);
        }

        $data = json_decode($request->get('data'));
        $file = $request->files->get('file');

        if($file){
            $filename = $fileUploader->upload($file, 'avatar/', true);
            $user->setAvatar($filename);
        }

        $user->setUsername($data->username->value);
        $user->setEmail($data->email->value);
        $user->setRoles($data->roles->value);

        $em->persist($user); $em->flush();
        return new JsonResponse(['code' => 1, 'highRoleCode' => $user->getHighRoleCode(), 'highRole' => $user->getHighRole(), 'avatar' => $user->getAvatar()]);
    }

    /**
     * @Route("/add/utilisateur", options={"expose"=true}, name="user_add")
     */
    public function add(Request $request, FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->get('data'));
        $file = $request->files->get('file');

        $user = (new User())
            ->setUsername($data->username->value)
            ->setEmail($data->email->value)
            ->setRoles($data->roles->value)
        ;
        $user->setPassword($passwordEncoder->encodePassword(
            $user, uniqid()
        ));

        if($file){
            $filename = $fileUploader->upload($file, 'avatar/', true);
            $user->setAvatar($filename);
        }

        $em->persist($user); $em->flush();
        return new JsonResponse(['code' => 1, 'highRoleCode' => $user->getHighRoleCode(), 'highRole' => $user->getHighRole(), 'avatar' => $user->getAvatar()]);
    }

    /**
     * @Route("/convert-is-new/utilisateur/{user}", options={"expose"=true}, name="user_convert_is_new")
     */
    public function convertIsNew($user, Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user);
        if(!$user){
            return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur n\'existe pas.']);
        }

        // Send mail
        $url = $this->generateUrl('app_password_unlock', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);        
        if($mailer->sendMail(
            'Création de mot de passe',
            'Lien de création de mot de passe',
            'root/super/email/security/unlock.html.twig',
            ['url' => $url],
            $user->getEmail()
        ) != true){
            return new JsonResponse([
                'code' => 2,
                'errors' => [ 'error' => 'Le service est indisponible', 'success' => '' ]
            ]);
        }

        $user->setIsNew(false);
        $em->persist($user); $em->flush();
        return new JsonResponse(['code' => 1]);
    }

    /**
     * @Route("/delete/utilisateur/{user}", options={"expose"=true}, name="user_delete")
     */
    public function delete($user)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user);
        if(!$user){
            return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur n\'existe pas.']);
        }

        if($user->getHighRoleCode() == User::CODE_ROLE_SUPER_ADMIN){
            return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur ne peut pas être supprimé.']);
        }

        $em->remove($user); $em->flush();
        return new JsonResponse(['code' => 1]);
    }

    /**
    * @Route("/export/utilisateurs/{format}", options={"expose"=true}, name="export")
    */
    public function export(Export $export, $format)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findBy(array(), array('username' => 'ASC'));
        $data = array();

        foreach ($users as $user) {
            $tmp = array(
                $user->getId(),
                $user->getUsername(),
                $user->getHighRole(),
                $user->getEmail(),
                date_format($user->getCreateAt(), 'd/m/Y'),
            );
            if(!in_array($tmp, $data)){
                array_push($data, $tmp);
            }
        }        

        if($format == 'excel'){
            $fileName = 'utilisateurs.xlsx';
            $header = array(array('ID', 'Nom utilisateur', 'Role', 'Email', 'Date de creation'));
        }else{
            $fileName = 'utilisateurs.csv';
            $header = array(array('id', 'username', 'role', 'email', 'createAt'));
        }

        $json = $export->createFile($format, 'Liste des utilisateurs', $fileName , $header, $data, 5, null);
        
        return new BinaryFileResponse($this->getParameter('private_directory'). 'export/' . $fileName);
    }
}
