<?php

namespace App\Controller\Api;

use App\Entity\Difficulty;
use App\Entity\EventPlayers as EntityEventPlayers;
use App\Repository\EventsRepository;

use App\Entity\Sport;
use App\Entity\Sex;
use App\Service\EventsManager;
use App\Entity\EventPlayers;
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
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/search")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSearch(
        Request $request,
        EntityManagerInterface $entityManager
    ){
        $data = json_decode($request->getContent(), true);
        
        $sportQ = $request->query->get('sport');
        $dateQ = $request->query->get('date');
        $timeQ = $request->query->get('time');
        $searchQ = $request->query->get('search');

        // si dateQ.length == 0, dateQ = null
        if ($dateQ != null) {
            if (strlen($dateQ) == 0) {
                $dateQ = null;
            }
        }
        

        // si timeQ.length == 0, timeQ = null
        if ($timeQ != null) {
            if (strlen($timeQ) == 0) {
                $timeQ = null;
            }
        }
        

        // si searchQ.length == 0, searchQ = null
        if ($searchQ != null) {
            if (strlen($searchQ) == 0) {
                $searchQ = null;
            }
        }
        

        // si sportQ.length == 0, sportQ = null
        if ($sportQ != null) {
            if (strlen($sportQ) == 0) {
                $sportQ = null;
            }
        }

        $eventRepository = $entityManager->getRepository(Events::class);
        $sportCenterRepository = $entityManager->getRepository(SportCenter::class);
        $sportRepository = $entityManager->getRepository(Sport::class);
        $eventPlayersRepository = $entityManager->getRepository(EventPlayers::class);

        $resultsSearch = [];
        $resultsDate = [];
        $resultsTime = [];
        $resultsSport = [];
        $results= [];

        // Búsqueda de eventos
        if (isset($searchQ)) {
            $events = $eventRepository->createQueryBuilder('e')
            ->where('e.name LIKE :search')
            ->setParameter('search', '%' . $searchQ . '%')
            ->getQuery()
            ->getResult();
            if(!empty($events)){
                foreach ($events as $event) {
                    $resultsSearch[] = $event;
                    $results[] = $event;
                }						
            }
        }

        

       // Búsqueda por nombre de centro deportivo
       if (isset($searchQ)) {
			$sportCentersName = $sportCenterRepository->createQueryBuilder('sc')
           ->where('sc.name LIKE :search')
           ->setParameter('search', '%' . $searchQ . '%')
           ->getQuery()
           ->getResult();
           
           if (!empty($sportCentersName)){
			$sportCentersName2 = $eventRepository->findBy(['fk_sportcenter' => $sportCentersName]);
           foreach ($sportCentersName2 as $sportCenterName2) {
               $resultsSearch[] = $sportCenterName2;
               $results[] = $sportCenterName2;
           }
       }
       }

        // Búsqueda por nombre personalizado de centro deportivo
        if (isset($searchQ)) {
            $sportCenterCustomName = $eventRepository->createQueryBuilder('e')
            ->where('e.sport_center_custom LIKE :search')
            ->setParameter('search', '%' . $searchQ . '%')
            ->getQuery()
            ->getResult();

            if (!empty($sportCenterCustomName)) {
                $sportCenterCustomName2 = $eventRepository->findBy(['fk_sportcenter' => $sportCenterCustomName]);
                foreach ($sportCenterCustomName2 as $sportCenterCustomName2) {
                    $resultsSearch[] = $sportCenterCustomName2;
                    $results[] = $sportCenterCustomName2;
                }
            }
            
        }

       // Búsqueda por dirección de centro deportivo
       if (isset($searchQ)) {
           $eventsAddress = $sportCenterRepository->createQueryBuilder('sc')
           ->where('sc.address LIKE :search')
           ->setParameter('search', '%' . $searchQ . '%')
           ->getQuery()
           ->getResult();
           if (!empty($eventsAddress)) {
               $firstEventAddress = reset($eventsAddress);
               $eventsAddress2 = $eventRepository->findBy(['fk_sportcenter' => $firstEventAddress->getId()]);
               foreach ($eventsAddress2 as $eventAddress2) {
                   $resultsSearch[] = $eventAddress2;
                   $results[] = $eventAddress2;
               }
           }
       }

    // Sport
        if (isset($sportQ)) {

            if ($searchQ!=null) {
                $sport = $sportRepository->findOneBy(['name' => $sportQ]);

                if ($sport) {
                    $eventsSport = $eventRepository->findBy(['fk_sport' => $sport->getId()]);

                   foreach ($resultsSearch as $result) {
                        foreach ($eventsSport as $eventSport) {
                            if ($result->getId() == $eventSport->getId()) {
                                $resultsSport[] = $eventSport;
                                $results[] = $eventSport;
                            }
                        }
                    }
                                            
                    
               // $resultsSport = array_merge($resultsSport, $eventsSport);
                $results = array_merge($results, $eventsSport);

                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsSport as $eventSport) {
                    $tempArray[$eventSport->getId()] = $eventSport;
                }
                $resultsSport = array_values($tempArray);
                $results = array_values($tempArray);
                
                  
                }

            }else {
                $sport = $sportRepository->findOneBy(['name' => $sportQ]);
            
                if ($sport) {
                    $eventsSport = $eventRepository->findBy(['fk_sport' => $sport]);
                    
                    foreach ($eventsSport as $eventSport) {
                        $resultsSport[] = $eventSport;
                        $results[] = $eventSport;
                    }   
                }
            
            }
        }

        
    // Date
         if (isset($dateQ)) {
            $date = new DateTime($dateQ);
            $eventsDate = $eventRepository->findBy(['date' => $date]);

            if ($searchQ && $sportQ){
                // Focus sportQ
                foreach ($resultsSport as $resultSport) {
                    foreach ($eventsDate as $eventDate) {
                        if ($resultSport->getId() == $eventDate->getId()) {
                            $resultsDate[] = $eventDate;
                            $results[] = $eventDate;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[$eventDate->getId()] = $eventDate;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif (!$searchQ && $sportQ){
                // Focus sportQ
                foreach ($resultsSport as $resultSport) {
                    foreach ($eventsDate as $eventDate) {
                        if ($resultSport->getId() == $eventDate->getId()) {
                            $resultsDate[] = $eventDate;
                            $results[] = $eventDate;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[$eventDate->getId()] = $eventDate;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif ($searchQ && !$sportQ){
                // Focus searchQ

                foreach ($resultsSearch as $resultSearch) {
                    foreach ($eventsDate as $eventDate) {
                        if ($resultSearch->getId() == $eventDate->getId()) {
                            $resultsDate[] = $eventDate;
                            $results[] = $eventDate;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[$eventDate->getId()] = $eventDate;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }else{
                // Unico
                $resultsDate = array_merge($resultsDate, $eventsDate);
                $results = array_merge($results, $eventsDate);
            }
            
        }
    // Time    
        if (isset($timeQ)) {
            $time = DateTime::createFromFormat('H:i:s', $timeQ);
            $eventsTime = $eventRepository->findBy(['time' => $time]);

            if ($searchQ && $sportQ && $dateQ){
                // Focus dateQ
                foreach ($resultsDate as $resultDate) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultDate->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif ($searchQ && $sportQ && !$dateQ){
                // Focus sportQ
                foreach ($resultsSport as $resultSport) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultSport->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif ($searchQ && !$sportQ && $dateQ){
                // Focus dateQ
                foreach ($resultsDate as $resultDate) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultDate->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif (!$searchQ && $sportQ && $dateQ){
                // Focus dateQ
                foreach ($resultsDate as $resultDate) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultDate->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif ($searchQ && !$sportQ && !$dateQ){
                // Focus searchQ
                foreach ($resultsSearch as $resultSearch) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultSearch->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }elseif (!$searchQ && $sportQ && !$dateQ){
                // Focus sportQ
                foreach ($resultsSport as $resultSport) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultSport->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);
                
            }elseif (!$searchQ && !$sportQ && $dateQ){
                // Focus dateQ
                foreach ($resultsDate as $resultDate) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultDate->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }else{
                // Unico
                $resultsTime = array_merge($resultsTime, $eventsTime);
                $results = array_merge($results, $eventsTime);
            }
            
        }

        if (!$results) {
    return new JsonResponse([
        'events' => [],
        'sport_centers' => []
    ]);
        } else {
            $datos=[
                'events' => [],
                'sport_centers' => []
            ];
            foreach($results as $result){
            $id = $result->getId();
            $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $id]);

            $eventPlayersA=[];
            $eventPlayersB=[];

            
            $numParticipantes=0;
            foreach ($eventPlayers as $eventPlayer) {
                $numParticipantes++;
                $allEventPlayers[] = [
                    'fk_person_id' => $eventPlayer->getFkPerson()->getId(),

                ];
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
            
            $duration = $result->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($result->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

            //fecha actual
            $dateNow = new \DateTime();

            

            if ($result->getFkSportcenter()){
                if ($result->getDate() < $dateNow){
                    continue;
                }

                $datos['events' ][] = [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'is_private' => $result->isIsPrivate(),
                    'details' => $result->getDetails(),
                    'price' => $result->getPrice(),
                    'date' => $result->getDate()->format('d/m/Y'),
                    'time' => $result->getTime()->format('H:i'),                
                    'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                    'duration' => $result->getDuration()->format('H:i'),
                    'number_players' => $result->getNumberPlayers(),
                    
                    'fk_sports_id' => $result->getFkSport() ?[
                        'id' => $result->getFkSport()->getId(),
                        'name' => $result->getFkSport()->getName(),
                        'image' => $result->getFkSport()->getImage()
                    ] : null,
                    'fk_sportcenter_id' => $result->getFkSportcenter() ? [
                        'id' => $result->getFkSportcenter()->getId(),
                        'name' => $result->getFkSportcenter()->getName() ? $result->getFkSportcenter()->getName() : null,
                        'municipality' => $result->getFkSportcenter()->getMunicipality() ? $result->getFkSportcenter()->getMunicipality() : null,
                        'address' => $result->getFkSportcenter()->getAddress() ? $result->getFkSportcenter()->getAddress() : null,
                        'image' => $result->getFkSportcenter()->getImage() ? $result->getFkSportcenter()->getImage() : null,
                        'phone' => $result->getFkSportcenter()->getPhone() ? $result->getFkSportcenter()->getPhone() : null,
                        'image_gallery1' => $result->getFkSportcenter()->getImageGallery1() ? $result->getFkSportcenter()->getImageGallery1() : null,
                        'image_gallery2' => $result->getFkSportcenter()->getImageGallery2() ? $result->getFkSportcenter()->getImageGallery2() : null,
                        'image_gallery3' => $result->getFkSportcenter()->getImageGallery3() ? $result->getFkSportcenter()->getImageGallery3() : null,
                        'image_gallery4' => $result->getFkSportcenter()->getImageGallery4() ? $result->getFkSportcenter()->getImageGallery4() : null,
                        'latitude' => $result->getFkSportcenter()->getLatitude() ? $result->getFkSportcenter()->getLatitude() : null,
                        'longitude' => $result->getFkSportcenter()->getLongitude() ? $result->getFkSportcenter()->getLongitude() : null,
                        'destination' => $result->getFkSportcenter()->getDestination() ? $result->getFkSportcenter()->getDestination() : null,
                    ] : null,
                    'fk_difficulty_id' => $result->getFkDifficulty() ?[
                        'id' => $result->getFkDifficulty()->getId(),
                        'type' => $result->getFkDifficulty()->getType(),
                    ] : null,
                    'fk_sex_id' => $result->getFkSex() ? [
                        'id' => $result->getFkSex()->getId(),
                        'gender' => $result->getFkSex()->getGender(),
                    ] : null,
                    'fk_person_id' => $result->getFkPerson() ? [
                        'id' => $result->getFkPerson()->getId(),
                    //    'image_profile' => $result->getFkPerson()->getImageProfile(),
                       'name_and_lastname' => $result->getFkPerson()->getNameAndLastname(),
                    
                        'fk_teamcolor_id' => $result->getFkTeamColor() ? [
                            'id' => $result->getFkTeamColor()->getId(),
                            'colour' => $result->getFkTeamColor()->getColour(),
                            'image_shirt' => $result->getFkTeamColor()->getImageShirt(),
                        ] : null,

                        'fk_teamcolor_two_id' => $result->getFkTeamcolorTwo() ? [
                            'id' => $result->getFkTeamcolorTwo()->getId(),
                            'colour' => $result->getFkTeamcolorTwo()->getColour(),
                            'image_shirt' => $result->getFkTeamcolorTwo()->getImageShirt(),
                        ] : null,

                    ] : null,
                    'event_players' => [
                        'event_players_A' => $eventPlayersA,
                        'event_players_B' => $eventPlayersB,
                    ],
                    'event_players_list' => $allEventPlayers,
                    'players_registered' => $numParticipantes,
                    'missing_players' => $result->getNumberPlayers() *2 - $numParticipantes,
                ];


                $datos['sport_centers'][] = [
                    'id' => $result->getFkSportcenter()->getId(),
                        'name' => $result->getFkSportcenter()->getName() ? $result->getFkSportcenter()->getName() : null,
                        'municipality' => $result->getFkSportcenter()->getMunicipality() ? $result->getFkSportcenter()->getMunicipality() : null,
                        'address' => $result->getFkSportcenter()->getAddress() ? $result->getFkSportcenter()->getAddress() : null,
                        'image' => $result->getFkSportcenter()->getImage() ? $result->getFkSportcenter()->getImage() : null,
                        'phone' => $result->getFkSportcenter()->getPhone() ? $result->getFkSportcenter()->getPhone() : null,
                        'image_gallery1' => $result->getFkSportcenter()->getImageGallery1() ? $result->getFkSportcenter()->getImageGallery1() : null,
                        'image_gallery2' => $result->getFkSportcenter()->getImageGallery2() ? $result->getFkSportcenter()->getImageGallery2() : null,
                        'image_gallery3' => $result->getFkSportcenter()->getImageGallery3() ? $result->getFkSportcenter()->getImageGallery3() : null,
                        'image_gallery4' => $result->getFkSportcenter()->getImageGallery4() ? $result->getFkSportcenter()->getImageGallery4() : null,
                        'latitude' => $result->getFkSportcenter()->getLatitude() ? $result->getFkSportcenter()->getLatitude() : null,
                        'longitude' => $result->getFkSportcenter()->getLongitude() ? $result->getFkSportcenter()->getLongitude() : null,
                        'destination' => $result->getFkSportcenter()->getDestination() ? $result->getFkSportcenter()->getDestination() : null,
                    

                ];
            }
            else{
                if ($result->getDate() < $dateNow){
                    continue;
                }
                $datos['events' ][] = [
                    'id' => $result->getId(),
                    'name' => $result->getName(),
                    'is_private' => $result->isIsPrivate(),
                    'details' => $result->getDetails(),
                    'price' => $result->getPrice(),
                    'date' => $result->getDate()->format('d/m/Y'),
                    'time' => $result->getTime()->format('H:i'),                
                    'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                    'duration' => $result->getDuration()->format('H:i'),
                    'number_players' => $result->getNumberPlayers(),
                    'sport_center_custom' => $result->getSportCenterCustom(),
                    'fk_sports_id' => $result->getFkSport() ?[
                        'id' => $result->getFkSport()->getId(),
                        'name' => $result->getFkSport()->getName(),
                        'image' => $result->getFkSport()->getImage()
                    ] : null,
                   // 'fk_sportcenter_id' => $result->getFkSportcenter() ? [
                   //     'id' => $result->getFkSportcenter()->getId(),
                   //     'fk_services_id' => $result->getFkSportcenter()->getFkServices() ? [
                   //         'id' => $result->getFkSportcenter()->getFkServices()->getId(),
                   //         'type' => $result->getFkSportcenter()->getFkServices()->getType()
                   //     ] : null,
                   //     'name' => $result->getFkSportcenter()->getName(),
                   //     'municipality' => $result->getFkSportcenter()->getMunicipality(),
                   //     'address' => $result->getFkSportcenter()->getAddress(),
                   //     'image' => $result->getFkSportcenter()->getImage(),
                   //     'phone' => $result->getFkSportcenter()->getPhone()
                   // ] : null,
                    'fk_difficulty_id' => $result->getFkDifficulty() ?[
                        'id' => $result->getFkDifficulty()->getId(),
                        'type' => $result->getFkDifficulty()->getType(),
                    ] : null,
                    'fk_sex_id' => $result->getFkSex() ? [
                        'id' => $result->getFkSex()->getId(),
                        'gender' => $result->getFkSex()->getGender(),
                    ] : null,
                    'fk_person_id' => $result->getFkPerson() ? [
                        'id' => $result->getFkPerson()->getId(),
                    //    'image_profile' => $result->getFkPerson()->getImageProfile(),
                       'name_and_lastname' => $result->getFkPerson()->getNameAndLastname(),
                    
                        'fk_teamcolor_id' => $result->getFkTeamColor() ? [
                            'id' => $result->getFkTeamColor()->getId(),
                            'colour' => $result->getFkTeamColor()->getColour(),
                            'image_shirt' => $result->getFkTeamColor()->getImageShirt(),
                        ] : null,

                        'fk_teamcolor_two_id' => $result->getFkTeamcolorTwo() ? [
                            'id' => $result->getFkTeamcolorTwo()->getId(),
                            'colour' => $result->getFkTeamcolorTwo()->getColour(),
                            'image_shirt' => $result->getFkTeamcolorTwo()->getImageShirt(),
                        ] : null,

                    ] : null,
                    'event_players' => [
                        'event_players_A' => $eventPlayersA,
                        'event_players_B' => $eventPlayersB,
                    ],
                    'event_players_list' => $allEventPlayers,
                    'players_registered' => $numParticipantes,
                    'missing_players' => $result->getNumberPlayers() *2 - $numParticipantes,
                ];

            
            }
            

            }
            return new JsonResponse($datos, Response::HTTP_OK);
          // return $results;
        }
        
        

    }

}
