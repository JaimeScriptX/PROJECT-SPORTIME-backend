<?php

namespace App\Controller\Api;

use App\Entity\Difficulty;
use App\Entity\EventPlayers as EntityEventPlayers;
use App\Repository\EventsRepository;
use App\Repository\EventsResultsRepository;
use App\Entity\Sport;
use App\Entity\EventsResults;
use App\Entity\Sex;
use App\Service\EventsManager;
use App\Entity\EventPlayers;
use App\Entity\ReservedTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
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
use DateInterval;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\Event;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;

class EventsController extends AbstractFOSRestController
{
    /**
     * getEventsSportime
     * 
     * Get all events of sportime from the database.
     * 
     * @OA\Tag(name="Events")
     * 
     * @OA\Response(
     *    response=200,
     *   description="Returns the events"
     * )
     * 
     * 
     * @Rest\Get(path="/events")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsSportime(
        EventsRepository $eventsRepository,
        EntityManagerInterface $entityManager,
        EventPlayersRepository $eventPlayersRepository,
        EventsResultsRepository $eventsResultsRepository
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
            $allEventPlayers=[];
            
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

            $duration = $event->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($event->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

           
             //get de las fotos de perfil con la url
             $getPhotoProfile = $event->getFkPerson()->getImageProfile();
             $photoProfile = $this->getParameter('url') . $getPhotoProfile;

              //get logo event
              $getLogoEvent = $event->getFkSport()->getLogoEvent();
              $LogoEvent = $this->getParameter('url') . $getLogoEvent;

              //get shirt
                $getShirt = $event->getFkTeamColor()->getImageShirt();
                $shirt = $this->getParameter('url') . $getShirt;
                
                $getShirtTwo = $event->getFkTeamColorTwo()->getImageShirt();
                $shirtTwo = $this->getParameter('url') . $getShirtTwo;
               //get imagesportcenter
                $fkSportCenter = $event->getFkSportcenter();
                $imageSportCenter = null;

                if ($fkSportCenter) {
                    $getImageSportCenter = $fkSportCenter->getImage();
                    $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                }


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
                    'logo_event' => $LogoEvent,
                ] : null,
                'fk_sportcenter_id' => $fkSportCenter ? [
                    'id' => $fkSportCenter->getId(),
                    'name' => $fkSportCenter->getName(),
                    'municipality' => $fkSportCenter->getMunicipality(),
                    'address' => $fkSportCenter->getAddress(),
                    'image' => $imageSportCenter,
                    'phone' => $fkSportCenter->getPhone(),
                    'latitude' => $fkSportCenter->getLatitude(),
                    'longitude' => $fkSportCenter->getLongitude(),
                    'destination' => $fkSportCenter->getDestination(),
                    'price' => $fkSportCenter->getPrice(),
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
                'events_results' => [
                    'team_a' => $resultA,
                    'team_b' => $resultB,
                ],
                'fk_teamcolor_two_id' => $event->getFkTeamcolorTwo() ? [
                    'id' => $event->getFkTeamcolorTwo()->getId(),
                    'colour' => $event->getFkTeamcolorTwo()->getColour(),
                    'image_shirt' => $shirtTwo,
                ] : null,
                'fk_person_id' => $event->getFkPerson() ? [
                    'id' => $event->getFkPerson()->getId(),
                    'image_profile' => $photoProfile,
                    'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                //    'birthday' => $event->getFkPerson()->getBirthday(),
                //    'weight' => $event->getFkPerson()->getWeight(),
                //    'height' => $event->getFkPerson()->getHeight(),
                //    'nationality' => $event->getFkPerson()->getNationality(),
                //    'fk_sex_id' => $event->getFkPerson()->getFkSex() ? [
                //        'id' => $event->getFkPerson()->getFkSex()->getId(),
                //        'gender' => $event->getFkPerson()->getFkSex()->getGender(),
                //    ] : null,
                    'fk_user_id' => [
                    //    'id' => $event->getFkPerson()->getFkUser()->getId(),
                    //    'email' => $event->getFkPerson()->getFkUser()->getEmail(),
                    //  'roles' => $event->getFkPerson()->getFkUser()->getRoles(),
                    //    'password' => $event->getFkPerson()->getFkUser()->getPassword(),
                        'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                    //    'name_and_lastname' => $event->getFkPerson()->getFkUser()->getNameAndLastname(),
                    //    'phone' => $event->getFkPerson()->getFkUser()->getPhone(),
                    ],
                    
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
        return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }

    /**
     * getEventsById
     * 
     * Get the event by id from the database.
     * 
     * @OA\Tag(name="Events") 
     * 
     * @OA\Parameter(
     *     name="id",
     *    in="path",
     *   description="Id of the event",
     *   @OA\Schema(type="chart", format="int32"),
     *  required=true
     * )
     * 
     * @OA\Response(
     *    response=200,
     *  description="Return the event"
     * )
     * 
     * @Rest\Get(path="/events/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsById(
        $id,
        EventsRepository $eventsRepository,
        EventPlayersRepository $eventPlayersRepository,
        EventsResultsRepository $eventsResultsRepository
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
            
            $duration = $event->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($event->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

             //get de las fotos de perfil con la url
             $getPhotoProfile = $event->getFkPerson()->getImageProfile();
             $photoProfile = $this->getParameter('url') . $getPhotoProfile;

              //get logo event
              $getLogoEvent = $event->getFkSport()->getLogoEvent();
              $LogoEvent = $this->getParameter('url') . $getLogoEvent;

            //get shirt
                $getShirt = $event->getFkTeamColor()->getImageShirt();
                $shirt = $this->getParameter('url') . $getShirt;
                
                $getShirtTwo = $event->getFkTeamColorTwo()->getImageShirt();
                $shirtTwo = $this->getParameter('url') . $getShirtTwo;

               //get image
                $fkSportCenter = $event->getFkSportcenter();
                $imageSportCenter = null;
                $cancelationReason = null;
                if ($fkSportCenter) {
                    $getImageSportCenter = $fkSportCenter->getImage();
                    $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;

                    $reservedTimeRepository = $this->getDoctrine()->getRepository(ReservedTime::class);
                    $reservedTime = $reservedTimeRepository->findOneBy(['fk_event_id' => $id]);
                    $cancelationReason = $reservedTime->getCancellationReason();
                }

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
                'fk_sports_id' => $event->getFkSport() ?[
                    'id' => $event->getFkSport()->getId(),
                    'name' => $event->getFkSport()->getName(),
                    'logo_event' => $LogoEvent,
                ] : null,
                'fk_sportcenter_id' => $fkSportCenter ? [
                    'id' => $fkSportCenter->getId(),
                    'name' => $fkSportCenter->getName(),
                    'municipality' => $fkSportCenter->getMunicipality(),
                    'address' => $fkSportCenter->getAddress(),
                    'image' => $imageSportCenter,
                    'phone' => $fkSportCenter->getPhone(),
                    'latitude' => $fkSportCenter->getLatitude(),
                    'longitude' => $fkSportCenter->getLongitude(),
                    'destination' => $fkSportCenter->getDestination(),
                    'price' => $fkSportCenter->getPrice(),
                ] : null,

                'cancellation_reason' => $cancelationReason,


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
                    'image_shirt' => $shirtTwo,
                ] : null,
                'events_results' => [
                    'team_a' => $resultA,
                    'team_b' => $resultB,
                ],
                'fk_person_id' => $event->getFkPerson() ? [
                    'id' => $event->getFkPerson()->getId(),
                    'image_profile' => $photoProfile,
                    'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                //    'birthday' => $event->getFkPerson()->getBirthday(),
                //    'weight' => $event->getFkPerson()->getWeight(),
                //    'height' => $event->getFkPerson()->getHeight(),
                //    'nationality' => $event->getFkPerson()->getNationality(),
                //    'fk_sex_id' => $event->getFkPerson()->getFkSex() ? [
                //        'id' => $event->getFkPerson()->getFkSex()->getId(),
                //        'gender' => $event->getFkPerson()->getFkSex()->getGender(),
                //    ] : null,
                    'fk_user_id' => [
                    //    'id' => $event->getFkPerson()->getFkUser()->getId(),
                    //    'email' => $event->getFkPerson()->getFkUser()->getEmail(),
                    //  'roles' => $event->getFkPerson()->getFkUser()->getRoles(),
                    //    'password' => $event->getFkPerson()->getFkUser()->getPassword(),
                        'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                    //    'name_and_lastname' => $event->getFkPerson()->getFkUser()->getNameAndLastname(),
                    //    'phone' => $event->getFkPerson()->getFkUser()->getPhone(),
                    ],
                    
                ] : null,
                'event_players' => [
                    'event_players_A' => $eventPlayersA,
                    'event_players_B' => $eventPlayersB,
                ],
                'event_players_list' => $allEventPlayers,
                'players_registered' => $numParticipantes,
                'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
            ];
        
        return new JsonResponse($data, Response::HTTP_OK);
        
    }


    /**
     * postEventsSportime
     * 
     * Create a new Sportime event.
     * 
     * @OA\Tag(name="Events")
     * 
     * @OA\RequestBody(
     *     required=true,
     *    description="Json con los datos del evento",
     *   @OA\JsonContent(
     * 
     *  @OA\Property(property="name", type="string", example="Partido de futbol"),
     *  @OA\Property(property="is_private", type="boolean", example="false"),
     *  @OA\Property(property="details", type="string", example="Partido de futbol 5"),
     *  @OA\Property(property="price", type="float", example="10.5"),
     *  @OA\Property(property="date", type="date", example="2021-05-05"),
     *  @OA\Property(property="time", type="time", example="12:00:00"),
     *  @OA\Property(property="duration", type="time", example="01:00:00"),
     *  @OA\Property(property="number_players", type="integer", example="10"),
     *  @OA\Property(property="fk_sport_id", type="chart", example="1"),
     *  @OA\Property(property="fk_sportcenter_id", type="chart", example="1"),
     *  @OA\Property(property="fk_difficulty_id", type="chart", example="1"),
     * 
     *    )
     * )
     * 
     * @OA\Response(
     *    response=200,
     *   description="Devuelve el evento creado",
     *  @OA\JsonContent(
     * 
     *  @OA\Property(property="id", type="chart", example="1"),
     * @OA\Property(property="name", type="string", example="Partido de futbol"),
     * @OA\Property(property="is_private", type="boolean", example="false"),
     * @OA\Property(property="details", type="string", example="Partido de futbol 5"),
     * @OA\Property(property="price", type="float", example="10.5"),
     * @OA\Property(property="date", type="date", example="2021-05-05"),
     * @OA\Property(property="time", type="time", example="12:00:00"),
     * @OA\Property(property="duration", type="time", example="01:00:00"),
     * @OA\Property(property="number_players", type="integer", example="10"),
     * @OA\Property(property="fk_sport_id", type="chart", example="1"),
     * @OA\Property(property="fk_sportcenter_id", type="chart", example="1"),
     * @OA\Property(property="fk_difficulty_id", type="chart", example="1"),
     * 
     * )
     * )
     * 
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

        $teamColor = $entityManager->getRepository(TeamColor::class)->findOneBy(['colour' => $data['fk_teamcolor']]);
        $events->setFkTeamcolor($teamColor);

        $teamColorTwo = $entityManager->getRepository(TeamColor::class)->findOneBy(['colour' => $data['fk_teamcolor_two']]);
        $events->setFkTeamcolorTwo($teamColorTwo);

        

        $entityManager->persist($events);
        $entityManager->flush();

        return $this->view($events, Response::HTTP_CREATED);

    }

    /**
     * postEventsCustom
     * 
     * Create a new Custom event.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Post(path="/eventsCustom")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventsCustom(
        Request $request,
        EntityManagerInterface $em
    ) {
        $entityManager = $this->getDoctrine()->getManager();



        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository(State::class)->find("d2e73d4c-0627-11ee-84aa-28e70f93b3c9"); //poner codigo uuid para estado de evento creado

        
        $events = new Events();
        $events->setName($data['name']);
        $events->setIsPrivate($data['is_private']);
        $events->setDetails($data['details']);
        $events->setPrice($data['price']);
        $events->setDate(new \DateTime($data['date']));
        $events->setTime(new \DateTime($data['time']));
        $events->setDuration(new \DateTime($data['duration']));
        $events->setNumberPlayers($data['number_players']);
        $events->setSportCenterCustom($data['sport_center_custom']);
        $events->setFkState($state);
     

        // fk
        $sport = $entityManager->getRepository(Sport::class)->findOneBy(['name' => $data['fk_sport']]);
        $events->setFkSport($sport);

        $difficulty = $entityManager->getRepository(Difficulty::class)->findOneBy(['type' => $data['fk_difficulty']]);
        $events->setFkDifficulty($difficulty);
    
        $sex = $entityManager->getRepository(Sex::class)->findOneBy(['gender' => $data['fk_sex']]);
        $events->setFkSex($sex);
        
        $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person']]);
        $events->setFkPerson($person);

        $teamColor = $entityManager->getRepository(TeamColor::class)->findOneBy(['colour' => $data['fk_teamcolor']]);
        $events->setFkTeamcolor($teamColor);

        $teamColorTwo = $entityManager->getRepository(TeamColor::class)->findOneBy(['colour' => $data['fk_teamcolor_two']]);
        $events->setFkTeamcolorTwo($teamColorTwo);

       
        $entityManager->persist($events);
        $entityManager->flush();

        

         // event_players
         $eventPlayer = new EventPlayers();
         $event = $entityManager->getRepository(Events::class)->find(['id' => $events->getId()]);
         $eventPlayer->setFkEvent($event);
 
         $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person']]);
         $eventPlayer->setFkPerson($person);
 
         $eventPlayer->setEquipo(1);
 
         $entityManager->persist($eventPlayer);
         $entityManager->flush();


         return $this->view($events, Response::HTTP_CREATED);
    }

    
    /**
     * putEventsSportime
     * 
     * Update a Sportime event.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Put(path="/eventsSportime/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventsSportime(
        Request $request, 
        $id,
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
        empty($data['fk_teamcolor_two']) ? true : $events->setFkTeamcolorTwo($data['fk_teamcolor_two']);

        $updatedEvents = $eventsRepository->updateEvents($events);

        return new JsonResponse(
            ['code' => 200, 'message' => 'Event updated successfully.'],
            Response::HTTP_OK
        );

    }
    

    /**
     * putEventsCustom
     * 
     * Update a Custom event.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Put(path="/eventsCustom/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventsCustom(
        Request $request, 
        $id,
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
        empty($data['fk_teamcolor_two']) ? true : $events->setFkTeamcolorTwo($data['fk_teamcolor_two']);
        
        

        $updatedEvents = $eventsRepository->updateEvents($events);

        return new JsonResponse(
            ['code' => 200, 'message' => 'Event updated successfully.'],
            Response::HTTP_OK
        );

    }

    /**
     * deleteEventsSportime
     * 
     * Delete a Sportime event.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Delete(path="/eventsSportime/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteEventsSportime(
        EntityManagerInterface $entityManager,
        Request $request,
        $id
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
     * getEventsByPersonId
     * 
     * Get all events by person id.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Get(path="/eventsPersona/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsByPersonId(
        $id,
        EventsRepository $eventsRepository,
        EventPlayersRepository $eventPlayersRepository,
        EventsResultsRepository $eventsResultsRepository
    ){
        $createdEvents = $eventsRepository->findBy(['fk_person' => $id]);
        $participatingEvents = $eventPlayersRepository->findBy(['fk_person' => $id]);
        $data = [
            'created_events' => [],
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
                if ($participatingEvent->getFkPerson()!=$participatingEvent->getFkEvent()->getFkPerson()){
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
                            'price' => $event->getFkSportcenter()->getPrice(),
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

                            // ordenar por date y time de menor a mayor
                            usort($data['participating_events'], function ($a, $b) {
                                $dateA = new \DateTime($a['date'] . ' ' . $a['time']);
                                $dateB = new \DateTime($b['date'] . ' ' . $b['time']);
                                return $dateA <=> $dateB;
                            });
                        }
                        else{
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
                                    'image_profile' =>  $photoProfile,
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
                            $photoProfile2 = $this->getParameter('url') . $getPhotoProfile;

                            //get logo event
                            $getLogoEvent = $event->getFkSport()->getLogoEvent();
                            $LogoEvent2 = $this->getParameter('url') . $getLogoEvent;

                            //get shirt
                            $getShirt = $event->getFkTeamColor()->getImageShirt();
                            $shirt = $this->getParameter('url') . $getShirt;

                             //get image
                            $fkSportCenter2 = $event->getFkSportcenter();
                            $imageSportCenter2 = null;

                        if ($fkSportCenter2) {
                            $getImageSportCenter = $fkSportCenter2->getImage();
                            $imageSportCenter2 = $this->getParameter('url') . $getImageSportCenter;
                            }
                            
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
                                    'image' => $event->getFkSport()->getImage(),
                                    'logo_event' =>  $LogoEvent2,
                                ] : null,
                                'fk_sportcenter_id' => $event->getFkSportcenter() ? [
                            'id' => $event->getFkSportcenter()->getId(),
                            'name' => $event->getFkSportcenter()->getName(),
                            'municipality' => $event->getFkSportcenter()->getMunicipality(),
                            'address' => $event->getFkSportcenter()->getAddress(),
                            'image' => $imageSportCenter2,
                            'phone' => $event->getFkSportcenter()->getPhone(),
                            'latitude' => $event->getFkSportcenter()->getLatitude(),
                            'longitude' => $event->getFkSportcenter()->getLongitude(),
                            'destination' => $event->getFkSportcenter()->getDestination(),
                            'price' => $event->getFkSportcenter()->getPrice(),
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
                               'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                                 'image_profile' => $photoProfile2,
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
                            
                            //ordenar por id de primero a ultimo
                            usort($data['created_events'], function ($a, $b) {
                                return $a['id'] <=> $b['id'];
                            });
                        }
            }             
            
            return new JsonResponse($data, Response::HTTP_OK);
        }
        
    }

    /**
     * postEventsResultsAction
     * 
     * Create a new events results.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Post(path="/eventsResults/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postEventsResultsAction(Request $request, $id)
    {
        //lo mas probable es que esta función no se utilice --------------------------------------------------------
        $event = $this->getDoctrine()->getRepository(Events::class)->find($id);
        if (empty($event)) {
            return new JsonResponse(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }else{
            $dataE = $this->getDoctrine()->getRepository(EventsResults::class)->findOneBy(['fk_event' => $id]);
            if (!empty($dataE)) {
            return new JsonResponse(['message' => 'EventsResults already exists'], Response::HTTP_CONFLICT);
            }else{
                $data = new EventsResults();
                $resultA = 0;
                $resultB = 0;

                $data->setTeamA($resultA);
                $data->setTeamB($resultB);
                $data->setFkEvent($event);
                $em = $this->getDoctrine()->getManager();
                $em->persist($data);
                $em->flush();

                //sumar 1 a todos los participantes del evento en el campo gamesPlayed de Person
                $eventPlayers = $this->getDoctrine()->getRepository(EventPlayers::class)->findBy(['fk_event' => $id]);
                foreach ($eventPlayers as $eventPlayer) {
                    $person = $this->getDoctrine()->getRepository(Person::class)->find($eventPlayer->getFkPerson()->getId());
                    $gamesPlayed = $person->getGamesPlayed();
                    $gamesPlayed = $gamesPlayed + 1;
                    $person->setGamesPlayed($gamesPlayed);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($person);
                    $em->flush();
                }

                return new JsonResponse(['status' => 'EventsResults created!'], Response::HTTP_CREATED);
            }
        }
    }


    /**
     * putEventsResultsAction
     * 
     * Update a events results.
     * 
     * @OA\Tag(name="Events")
     * 
     * @Rest\Put(path="/eventsResults/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function putEventsResultsAction(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $event = $this->getDoctrine()->getRepository(Events::class)->find($id);
        if (empty($event)) {
            return new JsonResponse(['message' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }else{
            $dataE = $this->getDoctrine()->getRepository(EventsResults::class)->findOneBy(['fk_event' => $id]);
            if (empty($dataE)) {
            return new JsonResponse(['message' => 'EventsResults not found'], Response::HTTP_NOT_FOUND);
            }else{
                //conseguir marcadores antes de actualizar
                $oldResultA = $dataE->getTeamA();
                $oldResultB = $dataE->getTeamB();

                $oldBestResult = 0;

                if ($oldResultA > $oldResultB) {
                    $oldBbestResult = 1;
                }elseif ($oldResultA < $oldResultB) {
                    $oldBbestResult = 2;
                }else{
                    $oldBbestResult = 0;
                }

                $resultA = $data['team_a'];
                $resultB = $data['team_b'];

                $bestResult = 0;

                if ($resultA > $resultB) {
                    $bestResult = 1;
                }elseif ($resultA < $resultB) {
                    $bestResult = 2;
                }else{
                    $bestResult = 0;
                }

                $dataE->setTeamA($resultA);
                $dataE->setTeamB($resultB);
                $em = $this->getDoctrine()->getManager();
                $em->persist($dataE);
                $em->flush();

                $eventPlayers = $this->getDoctrine()->getRepository(EventPlayers::class)->findBy(['fk_event' => $id]);
                foreach ($eventPlayers as $eventPlayer) {
                    $person = $this->getDoctrine()->getRepository(Person::class)->find($eventPlayer->getFkPerson()->getId());
                    $team = $eventPlayer->getEquipo();
                    $victories = $person->getVictories();
                    $defeat = $person->getDefeat();

                    if ($oldBbestResult == 0) {
                        if ($bestResult == 1) {
                            if ($team == 1) {
                                $victories = $victories + 1;
                            }else{
                                $defeat = $defeat + 1;
                            }
                        }elseif ($bestResult == 2) {
                            if ($team == 2) {
                                $victories = $victories + 1;
                            }else{
                                $defeat = $defeat + 1;
                            }
                        }
                    }elseif ($oldBbestResult == 1) {
                        if ($bestResult == 0) {
                            if ($team == 1) {
                                $victories = $victories - 1;
                            }else{
                                $defeat = $defeat - 1;
                            }
                        }elseif ($bestResult == 2) {
                            if ($team == 2) {
                                $victories = $victories + 1;
                                $defeat = $defeat - 1;
                            }else{
                                $victories = $victories - 1;
                                $defeat = $defeat + 1;
                            }
                        }
                    }elseif ($oldBbestResult == 2) {
                        if ($bestResult == 0) {
                            if ($team == 2) {
                                $victories = $victories - 1;
                            }else{
                                $defeat = $defeat - 1;
                            }
                        }elseif ($bestResult == 1) {
                            if ($team == 1) {
                                $victories = $victories + 1;
                                $defeat = $defeat - 1;
                            }else{
                                $victories = $victories - 1;
                                $defeat = $defeat + 1;
                            }
                        }
                    }
                        
                    $person->setVictories($victories);
                    $person->setDefeat($defeat);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($person);
                    $em->flush();
                }

                return new JsonResponse(['status' => 'EventsResults updated!'], Response::HTTP_CREATED);
            }
        }
    }
        
}