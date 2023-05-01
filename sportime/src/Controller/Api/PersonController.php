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
                'geight' => $person->getHeight(),
                'nationality' => $person->getNationality(),
                'fk_sex_id' => [
                    'id' => $sex->getId(),
                    'gender' => $sex->getGender(),
                ],
                'fk_user_id' => [
                    'id' => $person->getFkUser()->getId(),
                    'email' => $person->getFkUser()->getEmail(),
                    'roles' => $person->getFkUser()->getRoles(),
                    'password' => $person->getFkUser()->getPassword(),
                    'username' => $person->getFkUser()->getUsername(),
                    'name_and_lastname' => $person->getFkUser()->getNameAndLastname(),
                    'phone' => $person->getFkUser()->getPhone(),
                ],
            ];
        }
        
        return new JsonResponse($data);
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
        
        $entityManager->persist($person);
        $entityManager->flush();
        
        return $this->view($person, Response::HTTP_CREATED);
    }
}




