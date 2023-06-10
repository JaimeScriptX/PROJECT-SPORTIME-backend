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
use App\Entity\State;
use App\Entity\SportCenter;
use App\Entity\TeamColor;
use App\Form\Type\EventsFormType;
use App\Repository\EventPlayersRepository;
use App\Service\EventsFormProcessor;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\Event;
use OpenApi\Annotations as OA;



class EventPlayersController extends AbstractFOSRestController
{
    

    /**
     * getEventPlayers
     * 
     * Descripción
     * 
     * @OA\Tag(name="Event Players")
     * 
     * @OA\Response(
     *  response=200,
     * description="Returns all event players"
     * )
     * 
     * @Rest\Get(path="/eventPlayers")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     * 
     * 
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

                    //get de las fotos de perfil con la url
                    $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
                    $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                $data[] = [
                    'id' => $eventPlayer->getId(),
                    'fk_event_id' => $eventPlayer->getFkEvent()->getId(),
                    'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                    'team' => $eventPlayer->getEquipo(),
                    'image_profile' => $photoProfile ?? null,
                ];
            }
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * getEventPlayer
     * 
     * Descripción
     * 
     * @OA\Tag(name="Event Players")
     * 
     * @OA\Parameter(
     *    name="id",
     *   in="path",
     *   description="ID of the event player",
     *  @OA\Schema(type="char")
     * )
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns event player by event id"
     * )
     * 
     * @Rest\Get(path="/eventPlayers/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventPlayer(
        EventPlayersRepository $eventPlayersRepository,
        EntityManagerInterface $entityManager,
        $id
    ){
        $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);
        $eventPlayer = $eventPlayersRepository->find($id);

        if (!$eventPlayer) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No event player found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
            $photoProfile = $this->getParameter('url') . $getPhotoProfile;

            $data = [
                'id' => $eventPlayer->getId(),
                'fk_event_id' => $eventPlayer->getFkEvent()->getId(),
                'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                'team' => $eventPlayer->getEquipo(),
                'image_profile' => $photoProfile ?? null,
            ];
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * postEventPlayer
     * 
     * Descripción
     * 
     * @OA\Tag(name="Event Players")
     * 
     * @OA\RequestBody(
     *   required=true,
     *  description="JSON Payload",
     * @OA\JsonContent(
     *   required={"fk_event_id", "fk_person_id", "team"},
     * @OA\Property(property="fk_event_id", type="string"),
     * @OA\Property(property="fk_person_id", type="string"),
     * @OA\Property(property="team", type="int"),
     * )
     * )
     * 
     * @OA\Response(
     *  response=200,
     * description="Returns event player created"
     * )
     * 
     * @Rest\Post(path="/eventPlayers")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventPlayer(
        Request $request,
        EntityManagerInterface $entityManager
    ){
        $entityManager = $this->getDoctrine()->getManager();
        $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);
        $eventRepository = $entityManager->getRepository(Events::class);

        $data = json_decode($request->getContent(), true);

        $checkEventPlayer = $eventPlayersRepository->findOneBy([
            'fk_event' => $data['fk_event_id'],
            'fk_person' => $data['fk_person_id'],
        ]);

        if ($checkEventPlayer) {
            return new JsonResponse(
                ['code' => 409, 'message' => 'Event player already exists.'],
                Response::HTTP_CONFLICT
            );
        }
        else{
            $eventPlayer = new EventPlayers();
            $event = $entityManager->getRepository(Events::class)->find(['id' => $data['fk_event_id']]);
            $eventPlayer->setFkEvent($event);

            $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person_id']]);
            $eventPlayer->setFkPerson($person);

            $eventPlayer->setEquipo($data['team']);

            $entityManager->persist($eventPlayer);
            $entityManager->flush();

            $maxPlayers = $eventRepository->findOneBy(['id' => $data['fk_event_id']])->getNumberPlayers()*2;
            $players = $eventPlayersRepository->findBy(['fk_event' => $data['fk_event_id']]);
            $playersNumber = count($players);

            if($playersNumber == $maxPlayers){
                $event = $eventRepository->findOneBy(['id' => $data['fk_event_id']]);
                $state = $entityManager->getRepository(State::class)->find(['id' => "e94de16a-0627-11ee-84aa-28e70f93b3c9"]);
                $event->setFkState($state);
                $entityManager->persist($event);
                $entityManager->flush();
            }

            return new JsonResponse(
                ['code' => 201, 'message' => 'Event player created successfully.'],
                Response::HTTP_CREATED
            );
        }

        
    }

    /**
     * putEventPlayer
     * 
     * Descripción
     * 
     * @OA\Tag(name="Event Players")
     * 
     * @OA\Parameter(
     *   name="id",
     *  in="path",
     * description="ID of the event player",
     * @OA\Schema(type="char")
     * )
     * 
     * @OA\RequestBody(
     *  required=true,
     * description="JSON Payload",
     * @OA\JsonContent(
     * required={"fk_event_id", "fk_person_id", "team"},
     * @OA\Property(property="fk_event_id", type="string"),
     * @OA\Property(property="fk_person_id", type="string"),
     * @OA\Property(property="team", type="int"),
     * )
     * )
     * 
     * @OA\Response(
     * response=200,
     * description="Returns event player updated"
     * )
     * 
     * @Rest\Put(path="/eventPlayers/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventPlayer(
        Request $request,
        EntityManagerInterface $entityManager,
        $id
    ){
        $entityManager = $this->getDoctrine()->getManager();
        $eventPlayer = $entityManager->getRepository(EventPlayers::class)->find($id);

        if (!$eventPlayer) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No event player found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = json_decode($request->getContent(), true);

            $event = $entityManager->getRepository(Events::class)->find(['id' => $data['fk_event_id']]);
            $eventPlayer->setFkEvent($event);

            $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person_id']]);
            $eventPlayer->setFkPerson($person);

            $eventPlayer->setEquipo($data['team']);

            $entityManager->persist($eventPlayer);
            $entityManager->flush();

            return new JsonResponse(
                ['code' => 201, 'message' => 'Event player updated successfully.'],
                Response::HTTP_CREATED
            );
        }
    }

    /**
     * @OA\Tag(name="Event Players")
     * 
     * @Rest\Delete(path="/eventPlayers")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteEventPlayer(
        EntityManagerInterface $entityManager,
        Request $request
    ){
        $entityManager = $this->getDoctrine()->getManager();
        //encontrar evento por fk_event_id y fk_person_id
        $eventPlayer = $entityManager->getRepository(EventPlayers::class)->findOneBy([
            'fk_event' => $request->query->get('event_id'),
            'fk_person' => $request->query->get('person_id'),
        ]);

        if (!$eventPlayer) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No event player found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $entityManager->remove($eventPlayer);
            $entityManager->flush();

            $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);

            $maxPlayers = $eventPlayersRepository->findOneBy(['fk_event' => $request->query->get('event_id')])->getFkEvent()->getNumberPlayers()*2;
            $players = $eventPlayersRepository->findBy(['fk_event' => $request->query->get('event_id')]);
            $playersNumber = count($players);

            if($playersNumber < $maxPlayers){
                $event = $entityManager->getRepository(Events::class)->find(['id' => $request->query->get('event_id')]);
                $state = $entityManager->getRepository(State::class)->find(['id' => "d2e73d4c-0627-11ee-84aa-28e70f93b3c9"]);
                $event->setFkState($state);
                $entityManager->persist($event);
                $entityManager->flush();
            }

            return new JsonResponse(
                ['code' => 201, 'message' => 'Event player deleted successfully.'],
                Response::HTTP_CREATED
            );
        }
    }

}