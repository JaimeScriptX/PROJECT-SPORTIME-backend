<?php

namespace App\Controller\Api;

use App\Service\PersonFormProcessor;
use App\Service\PersonManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/persons")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getPerson(
        PersonManager $personManager
    ) {
        return $personManager->getRepository()->findAll();
    }

    /**
     * @Rest\Post(path="/persons")
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function PostPerson(
        PersonManager $personManager,
        PersonFormProcessor $personFormProcessor,
        Request $request
    ) {
        $person = $personManager->create();
        [$person, $error] = ($personFormProcessor)($person, $request);
        $statusCode = $person ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $person ?? $error;
        return View::create($data, $statusCode);
    }

    /**
     * @Rest\Get(path="/persons/{id}", requirements={"id"="|d+"})
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSinglePerson(
        int $id,
        PersonManager $personManager
    ) {
        $person = $personManager->find($id);
        if (!$person){
            return View::create('Person not found', Response::HTTP_BAD_REQUEST);
        }
        return $person;
    }

    /**
     * @Rest\Post(path="/persons/{id}", requirements={"id"="|d+"})
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function editPerson(
        int $id,
        PersonFormProcessor $personFormProcessor,
        PersonManager $personManager,
        Request $request
    ) {
        $person = $personManager->find($id);
        if (!$person){
            return View::create('Person not found', Response::HTTP_BAD_REQUEST);
        }
        [$person, $error] = ($personFormProcessor)($person, $request);
        $statusCode = $person ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $person ?? $error;
        return View::create($data, $statusCode);
    }

    /**
     * @Rest\Delete(path="/persons/{id}", requirements={"id"="|d+"})
     * @Rest\View(serializerGroups={"person"}, serializerEnableMaxDepthChecks=true)
     */
    public function deletePerson(
        int $id,
        PersonManager $personManager
    ) {
        $person = $personManager->find($id);
        if (!$person){
            return View::create('Person not found', Response::HTTP_BAD_REQUEST);
        }
        $personManager->delete($person);
        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}