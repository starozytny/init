<?php

namespace App\Controller\Super;

use App\Entity\User;
use App\Service\Export;
use App\Service\FileUploader;
use App\Service\Mailer;
use App\Service\SerializeData;
use App\Service\SettingsService;
use League\Csv\Reader;
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
    public function convertIsNew($user, Mailer $mailer, SettingsService $settingsService)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user);
        if(!$user){
            return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur n\'existe pas.']);
        }

        // Send mail
        $url = $this->generateUrl('app_password_unlock', ['token' => $user->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);        
        if($mailer->sendMail(
            'Création de mot de passe pour le site ' . $settingsService->getWebsiteName(),
            'Lien de création de mot de passe',
            'root/super/email/security/unlock.html.twig',
            ['url' => $url, 'user' => $user, 'settings' => $settingsService->getSettings()],
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
     * @Route("/delete-all/utilisateurs", options={"expose"=true}, name="user_delete_all")
     */
    public function deleteAll(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $selectors = $request->get('selectors');
        $usersId = explode(',', $selectors);

        $users = $em->getRepository(User::class)->findBy(['id' => $usersId]);
        if(count($users) == 0){
            return new JsonResponse(['code' => 0, 'message' => 'Aucun utilisateur sélectionné.']);
        }

        foreach($users as $user){
            if($user->getHighRoleCode() == User::CODE_ROLE_SUPER_ADMIN){
                return new JsonResponse(['code' => 0, 'message' => '[ERREUR] Cet utilisateur ne peut pas être supprimé.']);
            }
            $em->remove($user); $em->flush();
        }
       
        return new JsonResponse(['code' => 1]);
    }

    /**
    * @Route("/export/{format}", options={"expose"=true}, name="export")
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

        $json = $export->createFile($format, 'Liste des utilisateurs', $fileName , $header, $data, 5);
        
        return new BinaryFileResponse($this->getParameter('private_directory'). 'export/' . $fileName);
    }
    /**
    * @Route("/import", options={"expose"=true}, name="import")
    */
    public function import(Request $request, UserPasswordEncoderInterface $passwordEncoder, Export $export)
    {
        $em = $this->getDoctrine()->getManager();
        $poursuivre = intval($request->get('poursuivre'));    // if continue after anomalies
        $choice = intval($request->get('choice'));            // 0 ne pas ecraser | 1 ecraser
        $ecraser = 1;
        $file = $request->files->get('file');

        if($file == null){
            return new JsonResponse(['code' => 0, 'message' => 'Veuillez téléverser un fichier CSV.']);
        }

        $reader = Reader::createFromPath($file->getPathname());
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

        // stack in records JSON and gets doublons
        $records = [];
        $anomaliesCsv = [];
        $anomalies = [];
        
        foreach ($reader as $r){
            $record = json_decode(json_encode($r));
            $existe = false;

            // check data file
            if($record->username == "" || $record->email == ""){
                array_push($anomaliesCsv, array_values($r));
                array_push($anomalies, $record);
                $existe = true;
            }

            // check duplicate data
            foreach($records as $registered){
                if($registered->id == $record->id || $registered->username == $record->username || $registered->email == $record->email){
                    array_push($anomaliesCsv, array_values($r));
                    array_push($anomalies, $record);
                    $existe = true;
                }
            }

            if(!$existe){
                if(!in_array($record, $records)){
                    array_push($records, $record);
                }
            }            
        }

        if($poursuivre == 0){ // si first trigger import
            // Liste des erronées 
            if(!empty($anomalies)){

                $fileName = 'import-duplicate.csv';
                $header = $reader->getHeader();

                $export->createFile('csv', 'Liste des doublons', $fileName , array($header), $anomaliesCsv, count($header), 'import/users/duplicate/');
                $url = $this->generateUrl('super_users_import_duplicate', array(), UrlGeneratorInterface::ABSOLUTE_URL);
                return new JsonResponse(['code' => 0, 'message' => 'Le fichier contient des doublons ou des données sont manquants.', 'anomalies' => $anomalies, 'urlAnomalie' => $url, 'filename' => $fileName]);
            }
        }   

        // Processus de traitement des données sans les doublons
        $users = $em->getRepository(User::class)->findAll();
        foreach ($records as $record) {
            // check if existe
            $existe = false;
            foreach ($users as $user){
                if($user->getId() == $record->id || $user->getUsername() == mb_strtolower($record->username) || $user->getEmail() == $record->email){
                    $existe = true;
                    $userToEcrase = $user;
                }
            }

            if($existe){
                if($choice == $ecraser){ 
                    $user = $this->addUserWithCsv($passwordEncoder, $record, $userToEcrase);
                }
            }else{
                // nouveau car il n'existe pas dans la bdd
                $user = $this->addUserWithCsv($passwordEncoder, $record, null);
            }

            if($user){
                $em->persist($user);
            }

        }
        $em->flush();
        
        return new JsonResponse(['code' => 1]);
    }

     /**
     * @Route("/telecharger-doublon-import/", name="import_duplicate")
     */
    public function downloadDuplicateImport()
    {
        return new BinaryFileResponse($this->getParameter('private_directory'). 'export/import/users/duplicate/import-duplicate.csv');
    }

    private function addUserWithCsv(UserPasswordEncoderInterface $passwordEncoder, $record, $user)
    {
        if($user == null){
            $user = new User();
            $user->setPassword($passwordEncoder->encodePassword(
                $user, uniqid()
            ));
        }
        
        $user->setUsername($record->username);
        $user->setEmail($record->email);
        return $user;
    }
}
