<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Difficulty;
use App\Entity\EventPlayers;
use App\Repository\EventsRepository;

use App\Entity\Sport;
use App\Entity\Sex;
use App\Service\EventsManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Events;
use App\Entity\Person;
use App\Entity\SportCenter;
use App\Entity\TeamColor;
use App\Form\Type\EventsFormType;
use App\Repository\EventPlayersRepository;
use App\Service\EventsFormProcessor;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventPlayersController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/eventPlayers")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventPlayers(
        EventPlayersRepository $eventPlayersRepository,
        EntityManagerInterface $entityManager
    ){
        $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);
        $eventPlayers = $eventPlayersRepository->findAll();

        if (!$eventPlayers) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No event players found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = [];
            foreach ($eventPlayers as $eventPlayer) {
                $data[] = [
                    'id' => $eventPlayer->getId(),
                    'fk_event' => $eventPlayer->getFkEvent(),
                    'fk_person' => $eventPlayer->getFkPerson(),
                ];
            }
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Get(path="/eventPlayers/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventPlayer(
        EventPlayersRepository $eventPlayersRepository,
        EntityManagerInterface $entityManager,
        int $id
    ){
        $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);
        $eventPlayer = $eventPlayersRepository->find($id);

        if (!$eventPlayer) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No event player found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = [
                'id' => $eventPlayer->getId(),
                'fk_event' => $eventPlayer->getFkEvent(),
                'fk_person' => $eventPlayer->getFkPerson(),
            ];
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Post(path="/eventPlayers")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventPlayer(
        Request $request,
        EntityManagerInterface $entityManager
    ){
        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        $eventPlayer = new EventPlayers();
        $eventPlayer->setFkEvent($data['fk_event']);
        $eventPlayer->setFkPerson($data['fk_person']);

        $entityManager->persist($eventPlayer);
        $entityManager->flush();

        return new JsonResponse(
            ['code' => 201, 'message' => 'Event player created successfully.'],
            Response::HTTP_CREATED
        );
    }

}