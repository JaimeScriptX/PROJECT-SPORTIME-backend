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
            $sex = $person->getFkSex();
            $data[] = [
                'id' => $person->getId(),
                'image_profile' => $person->getImageProfile(),
                'name' => $person->getName(),
                'last_name' => $person->getLastName(),
                'birthday' => $person->getBirthday(),
                'weight' => $person->getWeight(),
                'height' => $person->getHeight(),
                'nationality' => $person->getNationality(),
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
                    'name_and_lastname' => $person->getFkUser()->getNameAndLastname(),
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
        PersonRepository $personRepository
    ){
        $person= $personRepository->find($id);

        if (!$person){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }

        $data = [
            'id' => $person->getId(),
            'image_profile' => $person->getImageProfile(),
            'name' => $person->getName(),
            'last_name' => $person->getLastName(),
            'birthday' => $person->getBirthday(),
            'weight' => $person->getWeight(),
            'height' => $person->getHeight(),
            'nationality' => $person->getNationality(),
            'fk_sex_id' => [
                'id' => $person->getFkSex()->getId(),
                'gender' => $person->getFkSex()->getGender(),
            ],
            'fk_user_id' => [
                'id' => $person->getFkUser()->getId(),
                'email' => $person->getFkUser()->getEmail(),
               // 'roles' => $person->getFkUser()->getRoles(),
                'password' => $person->getFkUser()->getPassword(),
                'username' => $person->getFkUser()->getUsername(),
                'name_and_lastname' => $person->getFkUser()->getNameAndLastname(),
                'phone' => $person->getFkUser()->getPhone(),
            ],
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
        $person->setName($data['name']);
        $person->setLastName($data['last_name']);
        $person->setBirthday(new \DateTime($data['birthday']));
        $person->setWeight($data['weight']);
        $person->setHeight($data['height']);
        $person->setNationality($data['nationality']);

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
    public function putEventsCustom(
        Request $request, 
        int $id,
        PersonRepository $personRepository
    ){
        $person = $personRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['image_profile']) ? true : $person->setImageProfile($data['image_profile']);
        empty($data['name']) ? true : $person->setName($data['name']);
        empty($data['last_name']) ? true : $person->setLastName($data['last_name']);
        empty($data['birthday']) ? true : $person->setBirthday(new \DateTime($data['birthday']));
        empty($data['weight']) ? true : $person->setWeight($data['weight']);
        empty($data['height']) ? true : $person->setHeight($data['height']);
        empty($data['nationality']) ? true : $person->setNationality($data['nationality']);

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
    public function deleteEventsSportime(
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ) {
        $personRepository = $entityManager->getRepository(Events::class);
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




