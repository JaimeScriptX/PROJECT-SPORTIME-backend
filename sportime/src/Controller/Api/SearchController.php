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
use App\Entity\EventsResults;
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
use openApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;


class SearchController extends AbstractFOSRestController
{
    /**
     * getSearch
     * 
     * Get events and sport centers according to the search parameters.
     * 
     * @OA\Tag(name="Search")
     * 
     *   @OA\Parameter(
     *  name="sport",
     *  in="query",
     *  description="Sport",
     *  required=true,
     *  @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(
     *  name="date",
     *  in="query",
     *  description="Date",
     *  required=true,
     *  @OA\Schema(type="string")
     *  ),
     *  @OA\Parameter(
     *  name="time",
     *  in="query",
     *  description="Time",
     *  required=true,
     *  @OA\Schema(type="string")
     *  ),
     * 
     *  @OA\Parameter(
     *  name="search",
     *  in="query",
     *  description="Search",
     *  required=true,
     *  @OA\Schema(type="string")
     *  ),
     *  )
     * 
     * @OA\Response(
     *  response=200,
     *  description="Returns events and sport centers according to the search parameters",
     *  )
     * 
     * 
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
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);

        $resultsSearch = [];
        $resultsDate = [];
        $resultsTime = [];
        $resultsSport = [];
        $results= [];
        $sportCenterbySport = [];

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
                    if ($event->getFkSportcenter() != null){
                        $sportCenterbySport[] = $sportCenterRepository->findOneBy(['id' => $event->getFkSportcenter()->getId()]);
                    }
                    
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
                    if ($sportCenterName2->getFkSportcenter() != null){
                        $sportCenterbySport[] = $sportCenterRepository->findOneBy(['id' => $sportCenterName2->getFkSportcenter()->getId()]);
                    }
                    
                    
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
                   $sportCenterbySport[] = $sportCenterRepository->findOneBy(['id' => $eventAddress2->getFkSportcenter()->getId()]);
                }
            }
            
            $eventsAddress = $sportCenterRepository->createQueryBuilder('sc')
           ->where('sc.municipality LIKE :search')
           ->setParameter('search', '%' . $searchQ . '%')
           ->getQuery()
           ->getResult();
           if (!empty($eventsAddress)) {
               $firstEventAddress = reset($eventsAddress);
               $eventsAddress2 = $eventRepository->findBy(['fk_sportcenter' => $firstEventAddress->getId()]);
               foreach ($eventsAddress2 as $eventAddress2) {
                   $resultsSearch[] = $eventAddress2;
                   $results[] = $eventAddress2;
                   $sportCenterbySport[] = $sportCenterRepository->findOneBy(['id' => $eventAddress2->getFkSportcenter()->getId()]);
                }
            }
            
            $eventsAddress = $sportCenterRepository->createQueryBuilder('sc')
           ->where('sc.municipality LIKE :search')
           ->setParameter('search', '%' . $searchQ . '%')
           ->getQuery()
           ->getResult();
           if (!empty($eventsAddress)) {
               $firstEventAddress = reset($eventsAddress);
               $eventsAddress2 = $sportCenterRepository->findBy(['id' => $firstEventAddress->getId()]);
               foreach ($eventsAddress2 as $eventAddress2) {
                   
                   $sportCenterbySport[] = $sportCenterRepository->findOneBy(['id' => $eventAddress2->getId()]);
                }
            }
            
            //borrar duplicados $resultsSearch y $results
            $temp = [];
            foreach ($resultsSearch as $key => $val) {
                if (!in_array($val, $temp)) {
                    $temp[$key] = $val;
                }
            }
            $resultsSearch = array_values($temp);

            $temp = [];
            foreach ($results as $key => $val) {
                if (!in_array($val, $temp)) {
                    $temp[$key] = $val;
                }
            }
            $results = array_values($temp);
            
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
                               // if ($eventSport->getFkSportcenter() != null){
                                //    $sportCenterbySport[] = $eventSport->getFkSportcenter()->getId();
                                //}
                                
                            }
                        }
                    }
                                            
                    // $resultsSport = array_merge($resultsSport, $eventsSport);
                    $results = array_merge($results, $eventsSport);

                    // Limpieza duplicados
                    $tempArray = [];
                    foreach ($resultsSport as $eventSport) {
                        $tempArray[] = $eventSport;
                    }
                    $resultsSport = array_values($tempArray);
                    $results = array_values($tempArray);
                }
            }else {
                $sport = $sportRepository->findOneBy(['name' => $sportQ]);
                
                if ($sport) {
                    $eventsSport = $eventRepository->findBy(['fk_sport' => $sport]);
                    $sportCenterSport = $sportCenterRepository->findAll();
                    // cosas nuevas
                    foreach ($sportCenterSport as $sportCenter) {
                        if ($sportCenter->getFkSport()->contains($sport)) {
                            $sportCenterbySport[] = $sportCenter;
                            
                            
                        }
                    }


                    foreach ($eventsSport as $eventSport) {
                        if ($eventSport->getName() != null) {
                            $resultsSport[] = $eventSport;
                            $results[] = $eventSport;
                        }
                        
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
                            if ($eventDate->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventDate->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[] = $eventDate;
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
                            if ($eventDate->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventDate->getFkSportcenter();
                            }
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[] = $eventDate;
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
                            if ($eventDate->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventDate->getFkSportcenter();
                            }
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[] = $eventDate;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
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
                            if ($eventTime->getFkSportcenter() != null){
                                $sportCenterbySport[] = $eventTime->getFkSportcenter();
                            }
                            
                        }
                    }
                }
                // Limpieza duplicados
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[] = $eventTime;
                }
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);

            }else{
                // Unico
                $resultsTime = array_merge($resultsTime, $eventsTime);
                $results = array_merge($results, $eventsTime);
            }
            
        }

        

        if (!$results && !$sportCenterbySport) {
    return new JsonResponse([
        'events' => [],
        'sport_centers' => []
    ]);
        } else {
            
            $datos=[
                'events' => [],
                'sport_centers' => []
            ];

            if ($results != null){

                // Limpieza de elementos vacios
                $tempArray = [];
                foreach ($results as $result) {
                    $tempArray[] = $result;
                }
                $results = array_values($tempArray);

                // Limpieza duplicados que tengan el mismo id
                $tempArray = [];
                foreach ($results as $event) {
                    $tempArray[] = $event;
                }
                $results = array_values($tempArray);

                foreach($results as $result){
                    if ($result->getId() == null){
                        continue;
                    }

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
                    $dateNow= $dateNow->format('Y-m-d');
                    //hora actual + 2h
                    $time = time();
                    $timeNow = date("H:i:s", $time + 7200);
    
                
                    if ($result->getFkSportcenter()!=null){
                        if ($dateQ!=null){
                            if ($result->getDate() < $dateNow){
                                continue;
                            }
                        }
               
                        $fkSportCenter = $result->getFkSportcenter();
                        $imageSportCenter = null;
    
                        if ($fkSportCenter) {
                            $getImageSportCenter = $fkSportCenter->getImage();
                            $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                        }
    
                        //if is_private 
                        if ($result->isIsPrivate()==true){
                            continue;
                        }
                        if ($result->getDate()->format('Y-m-d') >= $dateNow ){
                            $timeStart = $result->getTime()->format('H:i:s');
                            if ($result->getDate()->format('Y-m-d') == $dateNow && $timeStart < $timeNow){
                                continue;
                            }
                        else{
                            $datos['events'][] = [
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
                                    'image' => $imageSportCenter,
                                    'phone' => $result->getFkSportcenter()->getPhone() ? $result->getFkSportcenter()->getPhone() : null,
                                    'image_gallery1' => $result->getFkSportcenter()->getImageGallery1() ? $result->getFkSportcenter()->getImageGallery1() : null,
                                    'image_gallery2' => $result->getFkSportcenter()->getImageGallery2() ? $result->getFkSportcenter()->getImageGallery2() : null,
                                    'image_gallery3' => $result->getFkSportcenter()->getImageGallery3() ? $result->getFkSportcenter()->getImageGallery3() : null,
                                    'image_gallery4' => $result->getFkSportcenter()->getImageGallery4() ? $result->getFkSportcenter()->getImageGallery4() : null,
                                    'latitude' => $result->getFkSportcenter()->getLatitude() ? $result->getFkSportcenter()->getLatitude() : null,
                                    'longitude' => $result->getFkSportcenter()->getLongitude() ? $result->getFkSportcenter()->getLongitude() : null,
                                    'destination' => $result->getFkSportcenter()->getDestination() ? $result->getFkSportcenter()->getDestination() : null,
                                    'price' => $result->getFkSportcenter()->getPrice() ? $result->getFkSportcenter()->getPrice() : null,
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
                                //    'events_results' => [
                                //        'team_a' => $resultA,
                                //        'team_b' => $resultB,
                                //    ],
                                    'event_players_list' => $allEventPlayers,
                                    'players_registered' => $numParticipantes,
                                    'missing_players' => $result->getNumberPlayers() *2 - $numParticipantes,
                            ];
    
                            }
                        }
                    
    
                    //sport_centers tiene que ser unico por id
    
                    
    
                    $fkSportCenter = $result->getFkSportcenter();
                    $imageSportCenter = null;
    
                    if ($fkSportCenter) {
                        $getImageSportCenter = $fkSportCenter->getImage();
                        $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                    }
    
                    if ($datos['events'] == null && $timeQ == null){
                        continue;
                    }
                    if ($datos['events'] == null && $dateQ == null){
                        continue;
                    }
    
                    if ($sportCenterbySport != null){
                        continue;
                    }
    
                
    
                    
    
                    }
                    else{
                    if ($dateQ!=null){
    
                            if ($result->getDate() < $dateNow){
                                continue;
                            }
                    
                    }
                
                    if ($result->getDate()->format('Y-m-d') >= $dateNow){
                    $timeStart = $result->getTime()->format('H:i:s');
    
                
                    if ($result->getDate()->format('Y-m-d') == $dateNow && $timeStart < $timeNow){
                        continue;
                    }
                    else{
    
                        //if is_private 
                        if ($result->isIsPrivate()==true){
                            continue;
                        }
    
                        $datos['events'][] = [
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
                        //    'events_results' => [
                        //        'team_a' => $resultA,
                        //        'team_b' => $resultB,
                        //    ],
                            'event_players_list' => $allEventPlayers,
                            'players_registered' => $numParticipantes,
                            'missing_players' => $result->getNumberPlayers() *2 - $numParticipantes,
                        ];
                    }
                   
    
                    }
                 }
                }
            }

            
            //limpiar duplicados en $sportCenterbySport[]
            //$sportCenterbySport = array_unique($sportCenterbySport);
            //$sportCenterbySport = array_values($sportCenterbySport);
            
            
          
            if ($sportCenterbySport!=null){
             
                // Limpieza duplicados que tengan el mismo id
                $sportCenterbySport = array_map("unserialize", array_unique(array_map("serialize", $sportCenterbySport)));
                $sportCenterbySport = array_values($sportCenterbySport);
            
            

                foreach($sportCenterbySport as $center){
                    $fkSportCenter = $center;
                    $imageSportCenter = null;
    
                    if ($fkSportCenter) {
                        $getImageSportCenter = $fkSportCenter->getImage();
                        $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                    }
                    if (!$results){
                        
                    }

                    $datos['sport_centers'][] = [
                        'id' => $center->getId(),
                        'name' => $center->getName(),
                        'image' => $imageSportCenter ? $imageSportCenter : null,
                        'address' => $center->getAddress() ? $center->getAddress() : null,
                        'phone' => $center->getPhone() ? $center->getPhone() : null,
                        'municipality' => $center->getMunicipality() ? $center->getMunicipality() : null,
                        'image_gallery1' => $center->getImageGallery1() ? $center->getImageGallery1() : null,
                        'image_gallery2' => $center->getImageGallery2() ? $center->getImageGallery2() : null,
                        'image_gallery3' => $center->getImageGallery3() ? $center->getImageGallery3() : null,
                        'image_gallery4' => $center->getImageGallery4() ? $center->getImageGallery4() : null,
                        'latitude' => $center->getLatitude() ? $center->getLatitude() : null,
                        'longitude' => $center->getLongitude() ? $center->getLongitude() : null,
                        'destination' => $center->getDestination() ? $center->getDestination() : null,
                    ];
                }
            }

            
            return new JsonResponse($datos, Response::HTTP_OK);
          // return $results;
        }
        
        

    }

}