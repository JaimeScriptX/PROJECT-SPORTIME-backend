<?php

namespace App\Controller\Api;

use App\Repository\PersonRepository;

use App\Service\PersonManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Person;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Sex;
use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\EventsRepository;
use App\Repository\EventPlayersRepository;
use App\Repository\EventsResultsRepository;
use DateInterval;



class PersonController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/persons")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPerson(
        PersonRepository $personRepository,
        EntityManagerInterface $entityManager
    ) {
        $personRepository = $entityManager->getRepository(Person::class);
        $persons = $personRepository->findAll();

         
        if (!$persons) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No persons found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {


            $data = [];
            foreach ($persons as $person) {

       
             // Calcular el ratio de victorias y derrotas
            $gamesPlayed = $person->getGamesPlayed();
            $victories = $person->getVictories();
            $defeats = $person->getDefeat();

            if ($gamesPlayed === 0) {
                $winLossRatio = 0;
            } elseif ($defeats === 0 && $victories === 0) {
                $winLossRatio = 0;
            } 
            else {
                $winLossRatio = ($victories / ($victories + $defeats)) * 100;
            }
        
            // Redondear el resultado al número entero más cercano
            $winLossRatio = round($winLossRatio);
        

            //get de las fotos de perfil con la url
            $getPhotoProfile = $person->getImageProfile();
            $photoProfile = $this->getParameter('url') . $getPhotoProfile;
            
            //get de las fotos de banner con la url
            $getPhotoBanner = $person->getImageBanner();
            $photoBanner = $this->getParameter('url') . $getPhotoBanner;

             //Calcular la edad
             $age = null;
             $birthday = $person->getBirthday();
     
             if ($birthday !== null) {
                 $currentDate = new DateTime();
                 $dateBirth = new DateTime($birthday->format('Y-m-d'));
                 $difference = $dateBirth->diff($currentDate);
                 $age = $difference->y;
             }

            $sex = $person->getFkSex();
            $data[] = [
                'id' => $person->getId(),
                'image_profile' =>  $photoProfile,
                'name_and_lastname' => $person->getNameAndLastname(),
                'age' => $age,
                'birthday' => $birthday !== null ? $birthday->format('d-m-Y') : null,
                'weight' => $person->getWeight(),
                'height' => $person->getHeight(),
                'nationality' => $person->getNationality(),
                'city' => $person->getCity(),
                'games_played' => $person->getGamesPlayed(),
                'victories' => $person->getVictories(),
                'defeat' => $person->getDefeat(),
                'ratio' => $winLossRatio,
                'image_banner' => $photoBanner,
            
                'fk_sex_id' => [
                    'id' => $sex->getId(),
                    'gender' => $sex->getGender(),
                ],
                'fk_user_id' => [
                   // 'id' => $person->getFkUser()->getId(),
                   // 'email' => $person->getFkUser()->getEmail(),
                   // 'roles' => $person->getFkUser()->getRoles(),
                    // 'password' => $person->getFkUser()->getPassword(),
                    'username' => $person->getFkUser()->getUsername(),
                   // 'phone' => $person->getFkUser()->getPhone(),
                ],
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
        }

    }

    /**
     * @Rest\Get(path="/persons/{id}")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPersonById(
        $id,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    ){
        //$person= $personRepository->find($id);

        $person = $em->getRepository(Person::class)->findOneBy(['id' => $id]);

        if (!$person){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }

        if ($person === null){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Calcular el ratio de victorias y derrotas
        $gamesPlayed = $person->getGamesPlayed();
        $victories = $person->getVictories();
        $defeats = $person->getDefeat();

        if ($gamesPlayed === 0) {
            $winLossRatio = 0;
        } elseif ($defeats === 0 && $victories === 0) {
            $winLossRatio = 0;
        } 
        else {
            $winLossRatio = ($victories / ($victories + $defeats)) * 100;
        }
    
        // Redondear el resultado al número entero más cercano
        $winLossRatio = round($winLossRatio);
        
         //get de las fotos de perfil con la url
         $getPhotoProfile = $person->getImageProfile();
         $photoProfile = $this->getParameter('url') . $getPhotoProfile;
        
         //get de las fotos de banner con la url
         $getPhotoBanner = $person->getImageBanner();
         $photoBanner = $this->getParameter('url') . $getPhotoBanner;

         
        $age = null;
        $birthday = $person->getBirthday();

        if ($birthday !== null) {
            $currentDate = new DateTime();
            $dateBirth = new DateTime($birthday->format('Y-m-d'));
            $difference = $dateBirth->diff($currentDate);
            $age = $difference->y;
        }
         
        $data = [
            'id' => $person->getId(),
            'image_profile' => $photoProfile,
            'name_and_lastname' => $person->getNameAndLastname(),
            'age' => $age,
            'birthday' => $birthday !== null ? $birthday->format('d-m-Y') : null,
            'weight' => $person->getWeight(),
            'height' => $person->getHeight(),
            'nationality' => $person->getNationality(),
            'city' => $person->getCity(),
            'games_played' => $person->getGamesPlayed(),
            'victories' => $person->getVictories(),
            'defeat' => $person->getDefeat(),
            'ratio' => $winLossRatio,
            'image_banner' => $photoBanner,
            'fk_sex_id' => $person-> getFkSex() ? [
                'id' => $person->getFkSex()->getId(),
                'gender' => $person->getFkSex()->getGender()
            ] : null,
            'fk_user_id' => $person->getFkUser() ? [
               // 'id' => $person->getFkUser()->getId(),
               // 'email' => $person->getFkUser()->getEmail(),
               // 'roles' => $person->getFkUser()->getRoles(),
              //  'password' => $person->getFkUser()->getPassword(),
                'username' => $person->getFkUser()->getUsername(),
              //  'phone' => $person->getFkUser()->getPhone(),
            ] : null,
        ];
            
        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Rest\Get(path="/personsByName/{username}")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPersonByUsername(
        string $username,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    ){
        $personUser = $em->getRepository(User::class)->findOneBy(['username' => $username]);

        $person = $em->getRepository(Person::class)->findOneBy(['fk_user' => $personUser]);


        if (!$person){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }

        if ($person === null){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NOT_FOUND
            );
        }


        // Calcular el ratio de victorias y derrotas
        $gamesPlayed = $person->getGamesPlayed();
        $victories = $person->getVictories();
        $defeats = $person->getDefeat();

        if ($gamesPlayed === 0) {
            $winLossRatio = 0;
        } elseif ($defeats === 0 && $victories === 0) {
            $winLossRatio = 0;
        } 
        else {
            $winLossRatio = ($victories / ($victories + $defeats)) * 100;
        }

         //get de las fotos de perfil con la url
         $getPhotoProfile = $person->getImageProfile();
         $photoProfile = $this->getParameter('url') . $getPhotoProfile;
        
         //get de las fotos de banner con la url
         $getPhotoBanner = $person->getImageBanner();
         $photoBanner = $this->getParameter('url') . $getPhotoBanner;

          //Calcular la edad
          $age = null;
          $birthday = $person->getBirthday();
  
          if ($birthday !== null) {
              $currentDate = new DateTime();
              $dateBirth = new DateTime($birthday->format('Y-m-d'));
              $difference = $dateBirth->diff($currentDate);
              $age = $difference->y;
          }

        
        $data = [
            'id' => $person->getId(),
            'image_profile' => $photoProfile,
            'name_and_lastname' => $person->getNameAndLastname(),
            'age' => $age,
            'birthday' => $birthday !== null ? $birthday->format('d-m-Y') : null,
            'weight' => $person->getWeight(),
            'height' => $person->getHeight(),
            'nationality' => $person->getNationality(),
            'city' => $person->getCity(),
            'games_played' => $person->getGamesPlayed(),
            'victories' => $person->getVictories(),
            'ratio' =>  $winLossRatio,
            'defeat' => $person->getDefeat(),
            'image_banner' => $photoBanner,
            'fk_sex_id' => $person-> getFkSex() ? [
                'id' => $person->getFkSex()->getId(),
                'gender' => $person->getFkSex()->getGender()
            ] : null,
            'fk_user_id' => $person->getFkUser() ? [
             //   'id' => $person->getFkUser()->getId(),
              //  'email' => $person->getFkUser()->getEmail(),
               // 'roles' => $person->getFkUser()->getRoles(),
            //    'password' => $person->getFkUser()->getPassword(),
                'username' => $person->getFkUser()->getUsername(),
               // 'name_and_lastname' => $person->getFkUser()->getNameAndLastname(),
               // 'phone' => $person->getFkUser()->getPhone(),
            ] : null,
        ];
    
        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Rest\Post(path="/persons")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function postPerson(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ) {
        $entityManager = $this->getDoctrine()->getManager();
        
        $data = json_decode($request->getContent(), true);
        
        $person = new Person();
        // $person->setImageProfile($data['image_profile']);
        $person->setNameAndLastname($data['name_and_lastname']);
        $person->setBirthday(new \DateTime($data['birthday']));
        $person->setWeight($data['weight']);
        $person->setHeight($data['height']);
        $person->setNationality($data['nationality']);
        $person->setCity($data['city']);
        
        // $person->setGamesPlayed($data['games_played']);
        // $person->setVictories($data['victories']);
        // $person->setDefeat($data['defeat']);
        // $person->setImageBanner($data['image_banner']);
        // Manejar la carga de imagen del perfil

         // Manejar la carga de imagen del perfil
    if ($request->files->has('image_profile')) {
        /** @var UploadedFile $imageProfileFile */
        $imageProfileFile = $request->files->get('image_profile');

        $profileFilename = md5(uniqid()).'.'.$imageProfileFile->guessExtension();

        try {
            $imageProfileFile->move(
                $this->getParameter('app.upload_directory.profile'),
                $profileFilename
            );
            $person->setImageProfile('/images/profile/' . $profileFilename);
        } catch (FileException $e) {
            // Manejar la excepción en caso de error al mover el archivo
        }
    }
    // Manejar la carga de imagen del banner
    if ($request->files->has('image_banner')) {
        /** @var UploadedFile $imageBannerFile */
        $imageBannerFile = $request->files->get('image_banner');

        $bannerFilename = md5(uniqid()).'.'.$imageBannerFile->guessExtension();

        try {
            $imageBannerFile->move(
                $this->getParameter('app.upload_directory.banner'),
                $bannerFilename
            );
            $person->setImageBanner('/images/banner/' . $bannerFilename);
        } catch (FileException $e) {
            // Manejar la excepción en caso de error al mover el archivo
        }
    }

    
        // Agregar campo foráneo "sex"
        $sex = $entityManager->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]);
        $person->setFkSex($sex);

        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $data['fk_user']]);
        $person->setFkUser($user);
        
        $entityManager->persist($person);
        $entityManager->flush();
        
        return $this->view($person, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(path="/persons/{id}")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     * @IsGranted("ROLE_USER")
     */
    public function putPerson(
        Request $request, 
        $id,
        PersonRepository $personRepository,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        JWTTokenManagerInterface $jwtManager
    ){

        $person = $em->getRepository(Person::class)->find($id);
        
        $user = $tokenStorage->getToken()->getUser();


        if (!$person) {
            throw $this->createNotFoundException('Person not found');
        }
        
        $data = json_decode($request->getContent(), true);

        $birthday = (isset($data['birthday']))? new \DateTime($data['birthday']): $person->getBirthday();
        // $person->setImageProfile($data['image_profile']);
        $person->setNameAndLastname($data['name_and_lastname'] ?? $person->getNameAndLastname());
        $person->setBirthday($birthday);
        $person->setWeight($data['weight'] ?? $person->getWeight());
        $person->setHeight($data['height'] ?? $person->getHeight());
        $person->setNationality($data['nationality'] ?? $person->getNationality());
        $person->setCity($data['city'] ?? $person->getCity());
        // $person->setGamesPlayed($data['games_played']);
        // $person->setVictories($data['victories']);
        // $person->setDefeat($data['defeat']);
        // $person->setImageBanner($data['image_banner']);

        //FK
        $sex = (isset($data['fk_sex']))? $em->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]): $person->getFkSex();
        $person->setFkSex($sex);

        // $user = $em->getRepository(User::class)->findOneBy(['id' => $data['fk_user']]);
        // $person->setFkUser($user);
       
        // $user = $person->getFkUser();
        
        // // Establecer la relación
        // $person->setFkUser($user);

         // Manejar la carga de imagen del perfil
         if (isset($data['image_profile'])) {
            $imageData = $data['image_profile'];
            $imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);
            $imageData = imagecreatefromstring($imageData);
        
            $tmpImagePath = sys_get_temp_dir() . '/' . uniqid() . '.tmp';
            imagejpeg($imageData, $tmpImagePath);
        
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpImagePath);
            finfo_close($finfo);
        
            unlink($tmpImagePath);
        
            $extension = '';
            switch ($mimeType) {
                case 'image/jpeg':
                    $extension = 'jpg';
                    break;
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                // Agrega más casos según los formatos de imagen que quieras soportar
                // case 'image/bmp':
                //     $extension = 'bmp';
                //     break;
            }
        
            if (!empty($extension)) {
                $profileFilename = uniqid() . '.' . $extension;
                $profilePath = $this->getParameter('app.upload_directory.profile') . '/' . $profileFilename;
        
                switch ($extension) {
                    case 'jpg':
                        imagejpeg($imageData, $profilePath);
                        break;
                    case 'png':
                        imagepng($imageData, $profilePath);
                        break;
                    case 'gif':
                        imagegif($imageData, $profilePath);
                        break;
                    // Agrega más casos según los formatos de imagen que quieras soportar
                    // case 'bmp':
                    //     imagebmp($imageData, $profilePath);
                    //     break;
                }
        
                $person->setImageProfile('/images/profile/' . $profileFilename);
            }
        }
        

         

        // Manejar la carga de imagen del banner
        if (isset($data['image_banner'])) {
            $imageDataBanner = $data['image_banner'];
            $imageDataBanner = str_replace('data:image/png;base64,', '', $imageDataBanner);
            $imageDataBanner = str_replace(' ', '+', $imageDataBanner);
            $imageDataBanner = base64_decode($imageDataBanner);
            $imageDataBanner = imagecreatefromstring($imageDataBanner);
        
            $tmpImagePath = sys_get_temp_dir() . '/' . uniqid() . '.tmp';
            imagepng($imageDataBanner, $tmpImagePath);
        
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpImagePath);
            finfo_close($finfo);
        
            unlink($tmpImagePath);
        
            $extension = '';
            switch ($mimeType) {
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/jpeg':
                    $extension = 'jpg';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                // Agrega más casos según los formatos de imagen que quieras soportar
            }
        
            if (!empty($extension)) {
                $bannerFilename = uniqid() . '.' . $extension;
                $bannerPath = $this->getParameter('app.upload_directory.banner') . '/' . $bannerFilename;
        
                switch ($extension) {
                    case 'png':
                        imagepng($imageDataBanner, $bannerPath);
                        break;
                    case 'jpg':
                        imagejpeg($imageDataBanner, $bannerPath);
                        break;
                    case 'gif':
                        imagegif($imageDataBanner, $bannerPath);
                        break;
                    // Agrega más casos según los formatos de imagen que quieras soportar
                }
        
                $person->setImageBanner('/images/banner/' . $bannerFilename);
            }
        }
        
         
    if ($request->files->has('image_profile')) {
        /** @var UploadedFile $imageProfileFile */
        $imageProfileFile = $request->files->get('image_profile');

        $profileFilename = md5(uniqid()).'.'.$imageProfileFile->guessExtension();

        try {
            $imageProfileFile->move(
                $this->getParameter('app.upload_directory.profile'),
                $profileFilename
            );
            $person->setImageProfile('/images/profile/' . $profileFilename);
        } catch (FileException $e) {
            // Manejar la excepción en caso de error al mover el archivo
        }
    }

    // Manejar la carga de imagen del banner
    if ($request->files->has('image_banner')) {
        /** @var UploadedFile $imageBannerFile */
        $imageBannerFile = $request->files->get('image_banner');

        $bannerFilename = md5(uniqid()).'.'.$imageBannerFile->guessExtension();

        try {
            $imageBannerFile->move(
                $this->getParameter('app.upload_directory.banner'),
                $bannerFilename
            );
            $person->setImageBanner('/images/banner/' . $bannerFilename);
        } catch (FileException $e) {
            // Manejar la excepción en caso de error al mover el archivo
        }
    }

        $em->persist($person);
        $em->flush();

        $tokenWithProfile = $jwtManager->createFromPayload($user, [
            'image_profile' => $data['image_profile']
        ]);

        return new JsonResponse(
            ['code' => 200, 'message' => 'Person updated successfully.', 'token_with_profile' => $tokenWithProfile],
            Response::HTTP_OK
        );

    }

    /**
     * @Rest\Delete(path="/persons/{id}")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function deletePerson(
        EntityManagerInterface $entityManager,
        Request $request,
        $id
    ) {
        $personRepository = $entityManager->getRepository(Person::class);
        $person = $personRepository->find($id);
    
        if (!$person) {
            return new JsonResponse(
                ['code' => 404, 'message' => 'Person not found.'],
                Response::HTTP_NOT_FOUND
            );
        }
    
        $entityManager->remove($person);
        $entityManager->flush();
    
        return new JsonResponse(
            ['code' => 200, 'message' => 'Person deleted successfully.'],
            Response::HTTP_OK
        );
    }

    /**
     * @Rest\Get(path="/lastEventsPersona/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPersonLastEventsFinish(
        $id,
        EventsRepository $eventsRepository,
        EventPlayersRepository $eventPlayersRepository,
        EventsResultsRepository $eventsResultsRepository
    ){

        $participatingEvents = $eventPlayersRepository->findBy(['fk_person' => $id]);
        $data = [
            'participating_events' => [],
        ];

        if (!$participatingEvents) {
            return new JsonResponse($data, Response::HTTP_OK);
        }
        elseif ($eventPlayersRepository->findBy(['fk_person' => $id]) == null){
            //return error 204
            return new JsonResponse(
                ['code' => 204, 'message' => 'No events found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } 
        else {
           // $dataCreatedEvents = [];
           // $dataParticipatingEvents = [];
            

            foreach ($participatingEvents as $participatingEvent){
              //  if ($participatingEvent->getFkPerson()!=$participatingEvent->getFkEvent()->getFkPerson()){
                    $ids = $participatingEvent->getFkEvent()->getId();
                     $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $ids]);
                    $eventPlayersA=[];
                    $eventPlayersB=[];
        
        
                    $numParticipantes=0;
                    
                    foreach ($eventPlayers as $eventPlayer) {
                        $numParticipantes++;
                        $allEventPlayers[] = [
                            'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
        
                        ];
                        if ($eventPlayer->getEquipo() == 1){

                            //get de las fotos de perfil con la url
                            $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
                            $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                            $eventPlayersA[] = [
                                'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                'image_profile' => $photoProfile,
                            ];
        
                        }else{

                            //get de las fotos de perfil con la url
                            $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
                            $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                            $eventPlayersB[] = [
                                'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                'image_profile' => $photoProfile,
                            ];
                        }
                    }
                    $duration = $participatingEvent->getFkEvent()->getDuration();
                    $hours = $duration->format('H');
                    $minutes = $duration->format('i');

                    $event=$participatingEvent->getFkEvent();

                      //obtiene el marcador 
                      $idResult = $event->getId();
                      $ResultEvents = $eventsResultsRepository->findBy(['fk_event' => $idResult]);
          
                      $resultA = 0;
                      $resultB = 0;
          
                      if($ResultEvents){
                          foreach ($ResultEvents as $resultEvent) {
          
                              $resultA = $resultEvent->getTeamA();
                              $resultB = $resultEvent->getTeamB();
          
                          }
                      }

                     //get de las fotos de perfil con la url
                    $getPhotoProfile = $event->getFkPerson()->getImageProfile();
                    $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                    //get logo event
                    $getLogoEvent = $event->getFkSport()->getLogoEvent();
                    $LogoEvent = $this->getParameter('url') . $getLogoEvent;

                    //get shirt
                    $getShirt = $event->getFkTeamColor()->getImageShirt();
                    $shirt = $this->getParameter('url') . $getShirt;

                    //get image
                    $fkSportCenter = $event->getFkSportcenter();
                    $imageSportCenter = null;

                    


                
                    if ($fkSportCenter) {
                        $getImageSportCenter = $fkSportCenter->getImage();
                        $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                    }

                    $timeEnd = new \DateTime($participatingEvent->getFkEvent()->getTime()->format('H:i'));
                    $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
                    
                    //mostrar los eventos donde el state sea "012ae017-0628-11ee-84aa-28e70f93b3c9"
                    if ($event->getFkState()->getId() == "012ae017-0628-11ee-84aa-28e70f93b3c9"){
                        $data['participating_events'][] = [
                            'id' => $event->getId(),
                            'name' => $event->getName(),
                            'is_private' => $event->isIsPrivate(),
                            'details' => $event->getDetails(),
                            'price' => $event->getPrice(),
                            'date' => $event->getDate()->format('Y/m/d'),
                            'time' => $event->getTime()->format('H:i'),                
                            'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                            'duration' => $event->getDuration()->format('H:i'),
                            'number_players' => $event->getNumberPlayers(),
                            'sport_center_custom' => $event->getSportCenterCustom(),
                            'fk_sports_id' => $event->getFkSport() ?[
                                'id' => $event->getFkSport()->getId(),
                                'name' => $event->getFkSport()->getName(),
                                // 'image' => $event->getFkSport()->getImage(),
                                'logo_event' => $LogoEvent,
                            ] : null,
                            'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                                'id' => $event->getFkSportcenter()->getId(),
                                'name' => $event->getFkSportcenter()->getName(),
                                'municipality' => $event->getFkSportcenter()->getMunicipality(),
                                'address' => $event->getFkSportcenter()->getAddress(),
                                'image' => $imageSportCenter,
                                'phone' => $event->getFkSportcenter()->getPhone(),
                                'latitude' => $event->getFkSportcenter()->getLatitude(),
                                'longitude' => $event->getFkSportcenter()->getLongitude(),
                                'destination' => $event->getFkSportcenter()->getDestination(),
                            ] : null,
                            'fk_difficulty_id' => $event->getFkDifficulty() ?[
                                'id' => $event->getFkDifficulty()->getId(),
                                'type' => $event->getFkDifficulty()->getType(),
                            ] : null,
                            'fk_sex_id' => $event->getFkSex() ? [
                                'id' => $event->getFkSex()->getId(),
                                'gender' => $event->getFkSex()->getGender(),
                            ] : null,
                            'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                                'id' => $event->getFkTeamColor()->getId(),
                                'colour' => $event->getFkTeamColor()->getColour(),
                                'image_shirt' => $shirt,
                            ] : null,
                            'state' => $event->getFkState() ? [
                                'id' => $event->getFkState()->getId(),
                                'type' => $event->getFkState()->getType(),
                                'colour' => $event->getFkState()->getColour(),
                            ] : null,
                            'fk_teamcolor_two_id' => $event->getFkTeamcolorTwo() ? [
                                'id' => $event->getFkTeamcolorTwo()->getId(),
                                'colour' => $event->getFkTeamcolorTwo()->getColour(),
                                'image_shirt' => $shirt,
                            ] : null,
                            'events_results' => [
                                'team_a' => $resultA,
                                'team_b' => $resultB,
                            ],
                            'fk_person_id' => $event->getFkPerson() ? [
                                'id' => $event->getFkPerson()->getId(),
                                'image_profile' =>  $photoProfile,
                                'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(), 
                                'fk_user_id' => [
                                    'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                                ]
                            ] : null,
                            'event_players' => [
                                'event_players_A' => $eventPlayersA,
                                'event_players_B' => $eventPlayersB,
                            ],
                            'event_players_list' => $allEventPlayers,
                            'players_registered' => $numParticipantes,
                            'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
                                ];
                    }

                    

                            // ordenar por date y time de menor a mayor
                            usort($data['participating_events'], function ($a, $b) {
                                $dateA = new \DateTime($a['date'] . ' ' . $a['time']);
                                $dateB = new \DateTime($b['date'] . ' ' . $b['time']);
                                return $dateA <=> $dateB;
                            });
                        }
                        
                            
            }             
            
            return new JsonResponse($data, Response::HTTP_OK);
        
        
    }
}




