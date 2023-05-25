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
use App\Form\Type\PersonFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Sex;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Env\Env;
use DateTime;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;




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

            $victorias = $person->getVictories();
            $partidosJugados = $person->getGamesPlayed();
            $ratio = 0;

             // Calcular el ratio de victorias y derrotas
            $gamesPlayed = $person->getGamesPlayed();
            $victories = $person->getVictories();
            $defeats = $person->getDefeat();

            if ($gamesPlayed === 0) {
                $winLossRatio = 0;
            } else {
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
            $dateBirth = $person->getBirthday();
            $dateBirth = new DateTime($dateBirth->format('Y-m-d'));
            $currentDate = new DateTime();
            $diference = $dateBirth->diff($currentDate);
            $age = $diference->y;

            $sex = $person->getFkSex();
            $data[] = [
                'id' => $person->getId(),
                'image_profile' =>  $photoProfile,
                'name_and_lastname' => $person->getNameAndLastname(),
                'age' => $age,
                'birthday' => $person->getBirthday()->format('d-m-Y'),
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
        int $id,
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

        // Calcular el ratio de victorias y derrotas
        $gamesPlayed = $person->getGamesPlayed();
        $victories = $person->getVictories();
        $defeats = $person->getDefeat();

        if ($gamesPlayed === 0) {
            $winLossRatio = 0;
        } else {
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
           $dateBirth = $person->getBirthday();
           $dateBirth = new DateTime($dateBirth->format('Y-m-d'));
           $currentDate = new DateTime();
           $diference = $dateBirth->diff($currentDate);
           $age = $diference->y;
        
        $data = [
            'id' => $person->getId(),
            'image_profile' => $photoProfile,
            'name_and_lastname' => $person->getNameAndLastname(),
            'age' => $age,
            'birthday' => $person->getBirthday()->format('d-m-Y'),
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


        // Calcular el ratio de victorias y derrotas
        $gamesPlayed = $person->getGamesPlayed();
        $victories = $person->getVictories();
        $defeats = $person->getDefeat();

        if ($gamesPlayed === 0) {
            $winLossRatio = 0;
        } else {
            $winLossRatio = ($victories / ($victories + $defeats)) * 100;
        }

         //get de las fotos de perfil con la url
         $getPhotoProfile = $person->getImageProfile();
         $photoProfile = $this->getParameter('url') . $getPhotoProfile;
        
         //get de las fotos de banner con la url
         $getPhotoBanner = $person->getImageBanner();
         $photoBanner = $this->getParameter('url') . $getPhotoBanner;

          //Calcular la edad
          $dateBirth = $person->getBirthday();
          $dateBirth = new DateTime($dateBirth->format('Y-m-d'));
          $currentDate = new DateTime();
          $diference = $dateBirth->diff($currentDate);
          $age = $diference->y;

        
        $data = [
            'id' => $person->getId(),
            'image_profile' => $photoProfile,
            'name_and_lastname' => $person->getNameAndLastname(),
            'age' => $age,
            'birthday' => $person->getBirthday()->format('d-m-Y'),
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
     */
    public function putPerson(
        Request $request, 
        int $id,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    ){
        $person = $em->getRepository(Person::class)->find($id);


        if (!$person) {
            throw $this->createNotFoundException('Person not found');
        }
        
            

        $data = json_decode($request->getContent(), true);

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

        //FK
        $sex = $em->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]);
        $person->setFkSex($sex);

        // $user = $em->getRepository(User::class)->findOneBy(['id' => $data['fk_user']]);
        // $person->setFkUser($user);
       
        // $user = $person->getFkUser();
        
        // // Establecer la relación
        // $person->setFkUser($user);

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

        

        $em->persist($person);
        $em->flush();


        return new JsonResponse(
            ['code' => 200, 'message' => 'Person updated successfully.'],
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
        int $id
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
}




