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

            if($partidosJugados > 0 && $victorias > 0) {
            $ratio = $victorias / $partidosJugados;
             }
            

            $sex = $person->getFkSex();
            $data[] = [
                'id' => $person->getId(),
                'image_profile' => $person->getImageProfile(),
                'name_and_lastname' => $person->getNameAndLastname(),
                'birthday' => $person->getBirthday(),
                'weight' => $person->getWeight(),
                'height' => $person->getHeight(),
                'nationality' => $person->getNationality(),
                'city' => $person->getCity(),
                'games_played' => $person->getGamesPlayed(),
                'victories' => $person->getVictories(),
                'defeat' => $person->getDefeat(),
                'ratio' => $ratio,
                'image_banner' => $person->getImageBanner(),
            
                'fk_sex_id' => [
                    'id' => $sex->getId(),
                    'gender' => $sex->getGender(),
                ],
                'fk_user_id' => [
                    'id' => $person->getFkUser()->getId(),
                    'email' => $person->getFkUser()->getEmail(),
                   // 'roles' => $person->getFkUser()->getRoles(),
                    'password' => $person->getFkUser()->getPassword(),
                    'username' => $person->getFkUser()->getUsername(),
                    'phone' => $person->getFkUser()->getPhone(),
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

        
       
        $victorias = $person->getVictories();
        $partidosJugados = $person->getGamesPlayed();
        $ratio = 0;

        if($partidosJugados > 0 && $victorias > 0) {
        $ratio = $victorias / $partidosJugados;
         }
        
        

        $data = [
            'id' => $person->getId(),
            'image_profile' => $person->getImageProfile(),
            'name_and_lastname' => $person->getNameAndLastname(),
            'birthday' => $person->getBirthday(),
            'weight' => $person->getWeight(),
            'height' => $person->getHeight(),
            'nationality' => $person->getNationality(),
            'city' => $person->getCity(),
            'games_played' => $person->getGamesPlayed(),
            'victories' => $person->getVictories(),
            'defeat' => $person->getDefeat(),
            'ratio' => $ratio,
            'image_banner' => $person->getImageBanner(),
            'fk_sex_id' => $person-> getFkSex() ? [
                'id' => $person->getFkSex()->getId(),
                'gender' => $person->getFkSex()->getGender()
            ] : null,
            'fk_user_id' => $person->getFkUser() ? [
                'id' => $person->getFkUser()->getId(),
                'email' => $person->getFkUser()->getEmail(),
               // 'roles' => $person->getFkUser()->getRoles(),
                'password' => $person->getFkUser()->getPassword(),
                'username' => $person->getFkUser()->getUsername(),
                'phone' => $person->getFkUser()->getPhone(),
            ] : null,
        ];
    
        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Rest\Get(path="/personsByName/{name_and_lastname}")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPersonByName(
        string $name_and_lastname,
        PersonRepository $personRepository,
        EntityManagerInterface $em
    ){
        //$person= $personRepository->find($id);

        $person = $em->getRepository(Person::class)->findOneBy(['name_and_lastname' => $name_and_lastname]);



        if (!$person){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }


       
        $victorias = $person->getVictories();
        $partidosJugados = $person->getGamesPlayed();
        $ratio = 0;

        if($partidosJugados > 0 && $victorias > 0) {
        $ratio = $victorias / $partidosJugados;
        
         }
        
        $data = [
            'id' => $person->getId(),
            'image_profile' => $person->getImageProfile(),
            'name_and_lastname' => $person->getNameAndLastname(),
            'birthday' => $person->getBirthday(),
            'weight' => $person->getWeight(),
            'height' => $person->getHeight(),
            'nationality' => $person->getNationality(),
            'city' => $person->getCity(),
            'games_played' => $person->getGamesPlayed(),
            'victories' => $person->getVictories(),
            'ratio' => $ratio,
            'defeat' => $person->getDefeat(),
            'image_banner' => $person->getImageBanner(),
            'fk_sex_id' => $person-> getFkSex() ? [
                'id' => $person->getFkSex()->getId(),
                'gender' => $person->getFkSex()->getGender()
            ] : null,
           // 'fk_user_id' => $person->getFkUser() ? [
             //   'id' => $person->getFkUser()->getId(),
               // 'email' => $person->getFkUser()->getEmail(),
               // 'roles' => $person->getFkUser()->getRoles(),
            //    'password' => $person->getFkUser()->getPassword(),
               // 'username' => $person->getFkUser()->getUsername(),
               // 'name_and_lastname' => $person->getFkUser()->getNameAndLastname(),
               // 'phone' => $person->getFkUser()->getPhone(),
            //] : null,
        ];
    
        return new JsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @Rest\Post(path="/persons")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function postPerson(
        Request $request,
        EntityManagerInterface $em
    ) {
        $entityManager = $this->getDoctrine()->getManager();
        
        $data = json_decode($request->getContent(), true);
        
        $person = new Person();
        $person->setImageProfile($data['image_profile']);
        $person->setNameAndLastname($data['name_and_lastname']);
        $person->setBirthday(new \DateTime($data['birthday']));
        $person->setWeight($data['weight']);
        $person->setHeight($data['height']);
        $person->setNationality($data['nationality']);
        $person->setCity($data['city']);
        $person->setGamesPlayed($data['games_played']);
        $person->setVictories($data['victories']);
        $person->setDefeat($data['defeat']);
        $person->setImageBanner($data['image_banner']);
        
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
        PersonRepository $personRepository
    ){
        $person = $personRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['image_profile']) ? true : $person->setImageProfile($data['image_profile']);
        empty($data['name_and_lastname']) ? true : $person->setNameAndLastname($data['name_and_lastname']);
        empty($data['birthday']) ? true : $person->setBirthday(new \DateTime($data['birthday']));
        empty($data['weight']) ? true : $person->setWeight($data['weight']);
        empty($data['height']) ? true : $person->setHeight($data['height']);
        empty($data['nationality']) ? true : $person->setNationality($data['nationality']);
        empty($data['city']) ? true : $person->setCity($data['city']);
        empty($data['games_played']) ? true : $person->setGamesPlayed($data['games_played']);
        empty($data['victories']) ? true : $person->setVictories($data['victories']);
        empty($data['defeat']) ? true : $person->setDefeat($data['defeat']);
        empty($data['image_banner']) ? true : $person->setImageBanner($data['image_banner']);

        //fk
        empty($data['fk_sex']) ? true : $person->setFkSex($data['fk_sex']);
        empty($data['fk_user']) ? true : $person->setFkUser($data['fk_user']);

        $updatedPersons = $personRepository->updateEvents($person);

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




