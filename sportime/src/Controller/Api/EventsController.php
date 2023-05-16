<?php

namespace App\Controller\Api;

use App\Entity\Difficulty;
use App\Repository\EventsRepository;

use App\Entity\Sport;
use App\Entity\Sex;
use App\Service\EventsManager;
use App\Service\EventPlayers;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
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
use DateInterval;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventsController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsSportime(
        EventsRepository $eventsRepository,
        EntityManagerInterface $entityManager,
        EventPlayersRepository $eventPlayersRepository
    ) {
        $eventsRepository = $entityManager ->getRepository(Events::class);
        $events = $eventsRepository->findAll();

        if (!$events) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No events found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = [];
        foreach ($events as $event){
            $id = $event->getId();
            $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $id]);

            $eventPlayersA=[];
            $eventPlayersB=[];

            
            $numParticipantes=0;
            foreach ($eventPlayers as $eventPlayer) {
                $numParticipantes++;
                
                if ($eventPlayer->getEquipo() == 1){
                    $eventPlayersA[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                    ];

                }else{
                    $eventPlayersB[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                    ];
                }
            }
            
            $duration = $event->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($event->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

            
            $data[] =[
                'id' => $event->getId(),
                'name' => $event->getName(),
                'is_private' => $event->isIsPrivate(),
                'details' => $event->getDetails(),
                'price' => $event->getPrice(),
                'date' => $event->getDate()->format('d/m/Y'),
                'time' => $event->getTime()->format('H:i'),                
                'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                'duration' => $event->getDuration()->format('H:i'),
                'number_players' => $event->getNumberPlayers(),
                'sport_center_custom' => $event->getSportCenterCustom(),
                'fk_sports_id' => $event->getFkSport() ?[
                    'id' => $event->getFkSport()->getId(),
                    'name' => $event->getFkSport()->getName(),
                    'need_team' => $event->getFkSport()->isNeedTeam(),
                    'image' => $event->getFkSport()->getImage()
                ] : null,
                'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                    'id' => $event->getFkSportcenter()->getId(),
                    'fk_services_id' => $event->getFkSportcenter()->getFkServices() ? [
                        'id' => $event->getFkSportcenter()->getFkServices()->getId(),
                        'type' => $event->getFkSportcenter()->getFkServices()->getType()
                    ] : null,
                    'name' => $event->getFkSportcenter()->getName(),
                    'municipality' => $event->getFkSportcenter()->getMunicipality(),
                    'address' => $event->getFkSportcenter()->getAddress(),
                    'image' => $event->getFkSportcenter()->getImage(),
                    'phone' => $event->getFkSportcenter()->getPhone()
                ] : null,
                'fk_difficulty_id' => $event->getFkDifficulty() ?[
                    'id' => $event->getFkDifficulty()->getId(),
                    'type' => $event->getFkDifficulty()->getType(),
                ] : null,
                'fk_sex_id' => $event->getFkSex() ? [
                    'id' => $event->getFkSex()->getId(),
                    'gender' => $event->getFkSex()->getGender(),
                ] : null,
                'fk_person_id' => $event->getFkPerson() ? [
                    'id' => $event->getFkPerson()->getId(),
                //    'image_profile' => $event->getFkPerson()->getImageProfile(),
                   'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                //    'birthday' => $event->getFkPerson()->getBirthday(),
                //    'weight' => $event->getFkPerson()->getWeight(),
                //    'height' => $event->getFkPerson()->getHeight(),
                //    'nationality' => $event->getFkPerson()->getNationality(),
                //    'fk_sex_id' => $event->getFkPerson()->getFkSex() ? [
                //        'id' => $event->getFkPerson()->getFkSex()->getId(),
                //        'gender' => $event->getFkPerson()->getFkSex()->getGender(),
                //    ] : null,
                    //'fk_user_id' => [
                    //    'id' => $event->getFkPerson()->getFkUser()->getId(),
                    //    'email' => $event->getFkPerson()->getFkUser()->getEmail(),
                    //  'roles' => $event->getFkPerson()->getFkUser()->getRoles(),
                    //    'password' => $event->getFkPerson()->getFkUser()->getPassword(),
                    //    'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                    //    'name_and_lastname' => $event->getFkPerson()->getFkUser()->getNameAndLastname(),
                    //    'phone' => $event->getFkPerson()->getFkUser()->getPhone(),
                    //],
                    'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                        'id' => $event->getFkTeamColor()->getId(),
                        'team_a' => $event->getFkTeamColor()->getTeamA(),
                        'team_b' => $event->getFkTeamColor()->getTeamB(),
                    ] : null,
                ] : null,
                'event_players' => [
                    'event_players_A' => $eventPlayersA,
                    'event_players_B' => $eventPlayersB,
                ],

                'players_registered' => $numParticipantes,
                'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
                

                
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);

        
        }
        
        
    }

     /**
     * @Rest\Get(path="/events/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsById(
        int $id,
        EventsRepository $eventsRepository,
        EventPlayersRepository $eventPlayersRepository
        ){
            $event = $eventsRepository->find($id);
            $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $id]);

            if (!$event) {
                return new JsonResponse(
                    ['code' => 204, 'message' => 'No event found for this query.'],
                    Response::HTTP_NO_CONTENT
                );
            }
    
            $eventPlayersA=[];
            $eventPlayersB=[];


            $numParticipantes=0;
            foreach ($eventPlayers as $eventPlayer) {
                $numParticipantes++;
                
                if ($eventPlayer->getEquipo() == 1){
                    $eventPlayersA[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                    ];

                }else{
                    $eventPlayersB[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                    ];
                }
            }
            
            $duration = $event->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($event->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

            $data = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'is_private' => $event->isIsPrivate(),
                'details' => $event->getDetails(),
                'price' => $event->getPrice(),
                'date' => $event->getDate()->format('d/m/Y'),
                'time' => $event->getTime()->format('H:i'),                
                'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                'duration' => $event->getDuration()->format('H:i'),
                'number_players' => $event->getNumberPlayers(),
                'sport_center_custom' => $event->getSportCenterCustom(),
                'fk_sports_id' => $event->getFkSport() ? [
                    'id' => $event->getFkSport()->getId(),
                    'name' => $event->getFkSport()->getName(),
                    'need_team' => $event->getFkSport()->isNeedTeam(),
                    'image' => $event->getFkSport()->getImage()
                ] : null,
                'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                    'id' => $event->getFkSportcenter()->getId(),
                    'fk_services_id' => $event->getFkSportcenter()->getFkServices() ? [
                        'id' => $event->getFkSportcenter()->getFkServices()->getId(),
                        'type' => $event->getFkSportcenter()->getFkServices()->getType()
                    ] : null,
                    'name' => $event->getFkSportcenter()->getName(),
                    'municipality' => $event->getFkSportcenter()->getMunicipality(),
                    'address' => $event->getFkSportcenter()->getAddress(),
                    'image' => $event->getFkSportcenter()->getImage(),
                    'phone' => $event->getFkSportcenter()->getPhone()
                ] : null,
                'fk_difficulty_id' => $event->getFkDifficulty() ? [
                    'id' => $event->getFkDifficulty()->getId(),
                    'type' => $event->getFkDifficulty()->getType(),
                ] : null,
                'fk_sex_id' => $event->getFkSex() ? [
                    'id' => $event->getFkSex()->getId(),
                    'gender' => $event->getFkSex()->getGender(),
                ] : null,
                'fk_person_id' => $event->getFkPerson() ? [
                    'id' => $event->getFkPerson()->getId(),
                //    'image_profile' => $event->getFkPerson()->getImageProfile(),
                    'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                //    'birthday' => $event->getFkPerson()->getBirthday(),
                //    'weight' => $event->getFkPerson()->getWeight(),
                //    'geight' => $event->getFkPerson()->getHeight(),
                //    'nationality' => $event->getFkPerson()->getNationality(),
                //    'fk_sex_id' => $event->getFkPerson() ? [
                //        'id' => $event->getFkPerson()->getFkSex()->getId(),
                //        'gender' => $event->getFkPerson()->getFkSex()->getGender(),
                //    ] : null,
                    //'fk_user_id' => [
                    //    'id' => $event->getFkPerson()->getFkUser()->getId(),
                    //    'email' => $event->getFkPerson()->getFkUser()->getEmail(),
                    //  'roles' => $event->getFkPerson()->getFkUser()->getRoles(),
                    //    'password' => $event->getFkPerson()->getFkUser()->getPassword(),
                    //    'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                    //    'name_and_lastname' => $event->getFkPerson()->getFkUser()->getNameAndLastname(),
                    //    'phone' => $event->getFkPerson()->getFkUser()->getPhone(),
                    //],
                    'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                        'id' => $event->getFkTeamColor()->getId(),
                        'team_a' => $event->getFkTeamColor()->getTeamA(),
                        'team_b' => $event->getFkTeamColor()->getTeamB(),
                    ] : null,
                ] : null,
                
                'event_players' => [
                    'event_players_A' => $eventPlayersA,
                    'event_players_B' => $eventPlayersB,
                ],
                'players_registered' => $numParticipantes,
                'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,


            ];
        
        return new JsonResponse($data, Response::HTTP_OK);

        
        
    }


    

    

    /**
     * @Rest\Post(path="/eventsSportime")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventsSportime(
        Request $request,
        EntityManagerInterface $em
    ) {
        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        $events = new Events();
        $events->setName($data['name']);
        $events->setIsPrivate($data['is_private']);
        $events->setDetails($data['details']);
        $events->setPrice($data['price']);
        $events->setDate(new \DateTime($data['date']));
        $events->setTime(new \DateTime($data['time']));
        $events->setDuration(new \DateTime($data['duration']));
        $events->setNumberPlayers($data['number_players']);

        // fk
        $sport = $entityManager->getRepository(Sport::class)->findOneBy(['name' => $data['fk_sport']]);
        $events->setFkSport($sport);

        $sportCenter = $entityManager->getRepository(SportCenter::class)->findOneBy(['name' => $data['fk_sportcenter']]);
        $events->setFkSport($sportCenter);

        $difficulty = $entityManager->getRepository(Difficulty::class)->findOneBy(['type' => $data['fk_difficulty']]);
        $events->setFkDifficulty($difficulty);
    
        $sex = $entityManager->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]);
        $events->setFkSex($sex);
        
        $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person']]);
        $events->setFkPerson($person);

        $teamColor = $entityManager->getRepository(TeamColor::class)->find(['id' => $data['fk_teamcolor']]);
        $events->setFkTeamcolor($teamColor);

        $entityManager->persist($events);
        $entityManager->flush();

        return $this->view($events, Response::HTTP_CREATED);

    }

    /**
     * @Rest\Post(path="/eventsCustom")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventsCustom(
        Request $request,
        EntityManagerInterface $em
    ) {
        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        $events = new Events();
        $events->setName($data['name']);
        $events->setIsPrivate($data['is_private']);
        $events->setDetails($data['details']);
        $events->setPrice($data['price']);
        $events->setDate(new \DateTime($data['date']));
        $events->setTime(new \DateTime($data['time']));
        $events->setDuration(new \DateTime($data['duration']));
        $events->setNumberPlayers($data['number_players']);

        // fk
        $sport = $entityManager->getRepository(Sport::class)->findOneBy(['name' => $data['fk_sport']]);
        $events->setFkSport($sport);

        $difficulty = $entityManager->getRepository(Difficulty::class)->findOneBy(['type' => $data['fk_difficulty']]);
        $events->setFkDifficulty($difficulty);
    
        $sex = $entityManager->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]);
        $events->setFkSex($sex);
        
        $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person']]);
        $events->setFkPerson($person);

        $teamColor = $entityManager->getRepository(TeamColor::class)->find(['id' => $data['fk_teamcolor']]);
        $events->setFkTeamcolor($teamColor);

        $entityManager->persist($events);
        $entityManager->flush();

        return $this->view($events, Response::HTTP_CREATED);

    }

    
    /**
     * @Rest\Put(path="/eventsSportime/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventsSportime(
        Request $request, 
        int $id,
        EventsRepository $eventsRepository
        )
    {
        $events = $eventsRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['name']) ? true : $events->setName($data['name']);
        empty($data['is_private']) ? true : $events->setIsPrivate($data['is_private']);
        empty($data['details']) ? true : $events->setDetails($data['details']);
        empty($data['price']) ? true : $events->setPrice($data['price']);
        empty($data['date']) ? true : $events->setDate(new \DateTime($data['date']));
        empty($data['time']) ? true : $events->setTime(new \DateTime($data['time']));
        empty($data['duration']) ? true : $events->setDuration(new \DateTime($data['duration']));
        empty($data['number_players']) ? true : $events->setNumberPlayers($data['number_players']);

        // fk
        empty($data['fk_sport']) ? true : $events->setFkSport($data['fk_sport']);
        empty($data['fk_sportcenter']) ? true : $events->setFkSportcenter($data['fk_sportcenter']);
        empty($data['fk_difficulty']) ? true : $events->setFkDifficulty($data['fk_difficulty']);
        empty($data['fk_sex']) ? true : $events->setFkSex($data['fk_sex']);
        empty($data['fk_person']) ? true : $events->setFkPerson($data['fk_person']);
        empty($data['fk_teamcolor']) ? true : $events->setFkTeamcolor($data['fk_teamcolor']);

        $updatedEvents = $eventsRepository->updateEvents($events);

        return new JsonResponse(
            ['code' => 200, 'message' => 'Event updated successfully.'],
            Response::HTTP_OK
        );

    }
    

    /**
     * @Rest\Put(path="/eventsCustom/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventsCustom(
        Request $request, 
        int $id,
        EventsRepository $eventsRepository
        )
    {
        $events = $eventsRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['name']) ? true : $events->setName($data['name']);
        empty($data['is_private']) ? true : $events->setIsPrivate($data['is_private']);
        empty($data['details']) ? true : $events->setDetails($data['details']);
        empty($data['price']) ? true : $events->setPrice($data['price']);
        empty($data['date']) ? true : $events->setDate(new \DateTime($data['date']));
        empty($data['time']) ? true : $events->setTime(new \DateTime($data['time']));
        empty($data['duration']) ? true : $events->setDuration(new \DateTime($data['duration']));
        empty($data['number_players']) ? true : $events->setNumberPlayers($data['number_players']);

        // fk
        empty($data['fk_sport']) ? true : $events->setFkSport($data['fk_sport']);
        //empty($data['fk_sportcenter']) ? true : $events->setFkSportcenter($data['fk_sportcenter']);
        empty($data['fk_difficulty']) ? true : $events->setFkDifficulty($data['fk_difficulty']);
        empty($data['fk_sex']) ? true : $events->setFkSex($data['fk_sex']);
        empty($data['fk_person']) ? true : $events->setFkPerson($data['fk_person']);
        empty($data['fk_teamcolor']) ? true : $events->setFkTeamcolor($data['fk_teamcolor']);

        $updatedEvents = $eventsRepository->updateEvents($events);

        return new JsonResponse(
            ['code' => 200, 'message' => 'Event updated successfully.'],
            Response::HTTP_OK
        );

    }

    /**
    * @Rest\Delete(path="/eventsSportime/{id}")
    * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
    */
    public function deleteEventsSportime(
        EntityManagerInterface $entityManager,
        Request $request,
        int $id
    ) {
        $eventRepository = $entityManager->getRepository(Events::class);
        $event = $eventRepository->find($id);
    
        if (!$event) {
            return new JsonResponse(
                ['code' => 404, 'message' => 'Event not found.'],
                Response::HTTP_NOT_FOUND
            );
        }
    
        $entityManager->remove($event);
        $entityManager->flush();
    
        return new JsonResponse(
            ['code' => 200, 'message' => 'Event deleted successfully.'],
            Response::HTTP_OK
        );
    }

    /**
     * @Rest\Get(path="/eventsPersona/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsByPersonId(
        int $id,
        EventsRepository $eventsRepository,
        EventPlayersRepository $eventPlayersRepository
    ){
        $createdEvents = $eventsRepository->findBy(['fk_person' => $id]);
        $participatingEvents = $eventPlayersRepository->findBy(['fk_person' => $id]);

        if (!$participatingEvents) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No events found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
           // $dataCreatedEvents = [];
           // $dataParticipatingEvents = [];
            $data = [
                'created_events' => [],
                'participating_events' => [],
            ];

            foreach ($participatingEvents as $participatingEvent){
                if ($participatingEvent->getFkPerson()!=$participatingEvent->getFkEvent()->getFkPerson()){
                    $ids = $participatingEvent->getFkEvent()->getId();
                     $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $ids]);
                    $eventPlayersA=[];
                    $eventPlayersB=[];
        
        
                    $numParticipantes=0;
                    foreach ($eventPlayers as $eventPlayer) {
                        $numParticipantes++;
                        
                        if ($eventPlayer->getEquipo() == 1){
                            $eventPlayersA[] = [
                                'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                            ];
        
                        }else{
                            $eventPlayersB[] = [
                                'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                            ];
                        }
                    }
                    $duration = $participatingEvent->getFkEvent()->getDuration();
                    $hours = $duration->format('H');
                    $minutes = $duration->format('i');

                    $event=$participatingEvent->getFkEvent();

                    $timeEnd = new \DateTime($participatingEvent->getFkEvent()->getTime()->format('H:i'));
                    $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
                    $data['participating_events'][] = [
                        'id' => $event->getId(),
                        'name' => $event->getName(),
                        'is_private' => $event->isIsPrivate(),
                        'details' => $event->getDetails(),
                        'price' => $event->getPrice(),
                        'date' => $event->getDate()->format('d/m/Y'),
                        'time' => $event->getTime()->format('H:i'),                
                        'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                        'duration' => $event->getDuration()->format('H:i'),
                        'number_players' => $event->getNumberPlayers(),
                        'sport_center_custom' => $event->getSportCenterCustom(),
                        'fk_sports_id' => $event->getFkSport() ?[
                            'id' => $event->getFkSport()->getId(),
                            'name' => $event->getFkSport()->getName(),
                            'need_team' => $event->getFkSport()->isNeedTeam(),
                            'image' => $event->getFkSport()->getImage()
                        ] : null,
                        'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                            'id' => $event->getFkSportcenter()->getId(),
                            'fk_services_id' => $event->getFkSportcenter()->getFkServices() ? [
                                'id' => $event->getFkSportcenter()->getFkServices()->getId(),
                                'type' => $event->getFkSportcenter()->getFkServices()->getType()
                            ] : null,
                            'name' => $event->getFkSportcenter()->getName(),
                            'municipality' => $event->getFkSportcenter()->getMunicipality(),
                            'address' => $event->getFkSportcenter()->getAddress(),
                            'image' => $event->getFkSportcenter()->getImage(),
                            'phone' => $event->getFkSportcenter()->getPhone()
                        ] : null,
                        'fk_difficulty_id' => $event->getFkDifficulty() ?[
                            'id' => $event->getFkDifficulty()->getId(),
                            'type' => $event->getFkDifficulty()->getType(),
                        ] : null,
                        'fk_sex_id' => $event->getFkSex() ? [
                            'id' => $event->getFkSex()->getId(),
                            'gender' => $event->getFkSex()->getGender(),
                        ] : null,
                        'fk_person_id' => $event->getFkPerson() ? [
                            'id' => $event->getFkPerson()->getId(),
                           'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                            'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                                'id' => $event->getFkTeamColor()->getId(),
                                'team_a' => $event->getFkTeamColor()->getTeamA(),
                                'team_b' => $event->getFkTeamColor()->getTeamB(),
                            ] : null,
                        ] : null,
                        'event_players' => [
                            'event_players_A' => $eventPlayersA,
                            'event_players_B' => $eventPlayersB,
                        ],
                     
                        'players_registered' => $numParticipantes,
                        'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
                            ];
                        }
                        else{
                            $ids = $participatingEvent->getFkEvent()->getId();
                            $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $ids]);
                            $eventPlayersA=[];
                            $eventPlayersB=[];
        
        
                            $numParticipantes=0;
                            foreach ($eventPlayers as $eventPlayer) {
                                $numParticipantes++;
                        
                            if ($eventPlayer->getEquipo() == 1){
                                $eventPlayersA[] = [
                                    'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                    'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                                ];
                            
                            }else{
                                $eventPlayersB[] = [
                                    'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                                    'image_profile' => $eventPlayer->getFkPerson()->getImageProfile(),
                                ];
                            }
                            }   
                            $duration = $participatingEvent->getFkEvent()->getDuration();
                            $hours = $duration->format('H');
                            $minutes = $duration->format('i');
                         
                            $event=$participatingEvent->getFkEvent();

                            $timeEnd = new \DateTime($participatingEvent->getFkEvent()->getTime()->format('H:i'));
                            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
                            $data['created_events'][] = [
                                'id' => $event->getId(),
                        'name' => $event->getName(),
                        'is_private' => $event->isIsPrivate(),
                        'details' => $event->getDetails(),
                        'price' => $event->getPrice(),
                        'date' => $event->getDate()->format('d/m/Y'),
                        'time' => $event->getTime()->format('H:i'),                
                        'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                        'duration' => $event->getDuration()->format('H:i'),
                        'number_players' => $event->getNumberPlayers(),
                        'sport_center_custom' => $event->getSportCenterCustom(),
                        'fk_sports_id' => $event->getFkSport() ?[
                            'id' => $event->getFkSport()->getId(),
                            'name' => $event->getFkSport()->getName(),
                            'need_team' => $event->getFkSport()->isNeedTeam(),
                            'image' => $event->getFkSport()->getImage()
                        ] : null,
                        'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                            'id' => $event->getFkSportcenter()->getId(),
                            'fk_services_id' => $event->getFkSportcenter()->getFkServices() ? [
                                'id' => $event->getFkSportcenter()->getFkServices()->getId(),
                                'type' => $event->getFkSportcenter()->getFkServices()->getType()
                            ] : null,
                            'name' => $event->getFkSportcenter()->getName(),
                            'municipality' => $event->getFkSportcenter()->getMunicipality(),
                            'address' => $event->getFkSportcenter()->getAddress(),
                            'image' => $event->getFkSportcenter()->getImage(),
                            'phone' => $event->getFkSportcenter()->getPhone()
                        ] : null,
                        'fk_difficulty_id' => $event->getFkDifficulty() ?[
                            'id' => $event->getFkDifficulty()->getId(),
                            'type' => $event->getFkDifficulty()->getType(),
                        ] : null,
                        'fk_sex_id' => $event->getFkSex() ? [
                            'id' => $event->getFkSex()->getId(),
                            'gender' => $event->getFkSex()->getGender(),
                        ] : null,
                        'fk_person_id' => $event->getFkPerson() ? [
                            'id' => $event->getFkPerson()->getId(),
                           'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                            'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                                'id' => $event->getFkTeamColor()->getId(),
                                'team_a' => $event->getFkTeamColor()->getTeamA(),
                                'team_b' => $event->getFkTeamColor()->getTeamB(),
                            ] : null,
                        ] : null,
                        'event_players' => [
                            'event_players_A' => $eventPlayersA,
                            'event_players_B' => $eventPlayersB,
                        ],
                     
                        'players_registered' => $numParticipantes,
                        'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
                            
                            ];
                        }
            }             
            
            
        }
        
        
        return new JsonResponse($data, Response::HTTP_OK);

        
    }
        
}

