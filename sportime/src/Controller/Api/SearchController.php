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

        $eventRepository = $entityManager->getRepository(Events::class);
        $sportCenterRepository = $entityManager->getRepository(SportCenter::class);
        $sportRepository = $entityManager->getRepository(Sport::class);

        $resultsSearch = [];
        $resultsDate = [];
        $resultsTime = [];
        $resultsSport = [];
        $results= [];

        // Búsqueda de eventos
        if (isset($data['search'])) {
            $events = $eventRepository->findBy(['name' => $data['search']]);
            $resultsSearch = array_merge($resultsSearch, $events);
            $results = array_merge($results, $events);
        }

        

        // Búsqueda por nombre de centro deportivo
        if (isset($data['search'])) {
            $sportCentersName = $sportCenterRepository->findBy(['name' => $data['search']]);
            $resultsSearch = array_merge($resultsSearch, $sportCentersName);
            $results = array_merge($results, $sportCentersName);
        }

        // Búsqueda por nombre personalizado de centro deportivo
        if (isset($data['search'])) {
            $sportCenterCustomName = $eventRepository->findBy(['sport_center_custom' => $data['search']]);
            $resultsSearch = array_merge($resultsSearch, $sportCenterCustomName);
            $results = array_merge($results, $sportCenterCustomName);
        }

        // Búsqueda por dirección de centro deportivo
        if (isset($data['search'])) {
            $eventsAddress = $sportCenterRepository->findBy(['address' => $data['search']]);
    
            if (!empty($eventsAddress)) {
                $firstEventAddress = reset($eventsAddress);
                $eventsAddress2 = $eventRepository->findBy(['fk_sportcenter' => $firstEventAddress->getId()]);
                $resultsSearch = array_merge($resultsSearch, $eventsAddress2);
                $results = array_merge($results, $eventsAddress2);
            }
        }

       // Búsqueda por deporte
        if (isset($data['sport'])) {
            $sport = $sportRepository->findOneBy(['name' => $data['sport']]);

    
            if ($sport) {
                $eventsSport = $eventRepository->findBy(['fk_sport' => $sport->getId()]);

                if ($resultsSearch == null) {
                    $resultsSport = array_merge($resultsSport, $eventsSport);
                    $results = array_merge($results, $eventsSport);
                } else {
                    foreach ($resultsSearch as $resultSearch) {
                        foreach ($eventsSport as $eventSport) {
                            if ($resultSearch->getId() == $eventSport->getId()) {
                                $resultsSport[] = $eventSport;
                                $results[] = $eventSport;
                            }
                        }
                    }
                    $tempArray = [];
                    foreach ($resultsSport as $eventSport) {
                        $tempArray[$eventSport->getId()] = $eventSport;
                    }

                    // Obtener los elementos únicos sin duplicados
                    $resultsSport = array_values($tempArray);
                    $results = array_values($tempArray);
                }
            }
        }

         // Búsqueda por fecha y hora dentro de los eventos con el formato de la fecha "2023-04-28"
         if (isset($data['date'])) {
            $date = new DateTime($data['date']);
            $eventsDate = $eventRepository->findBy(['date' => $date]);
            
            if ($resultsSport == null) {
                $resultsDate = array_merge($resultsDate, $eventsDate);
                $results = array_merge($results, $eventsDate);
            } else {
                foreach ($resultsSport as $resultSport) {
                    foreach ($eventsDate as $eventDate) {
                        if ($resultSport->getId() == $eventDate->getId()) {
                            $resultsDate[] = $eventDate;
                            $results[] = $eventDate;
                        }
                    }
                }
                $tempArray = [];
                foreach ($resultsDate as $eventDate) {
                    $tempArray[$eventDate->getId()] = $eventDate;
                }

                // Obtener los elementos únicos sin duplicados
                $resultsDate = array_values($tempArray);
                $results = array_values($tempArray);
            }
            
        }
        
        if (isset($data['time'])) {
            $time = DateTime::createFromFormat('H:i:s', $data['time']);
            $eventsTime = $eventRepository->findBy(['time' => $time]);
            
            if ($resultsDate == null) {
                $resultsTime = array_merge($resultsTime, $eventsTime);
                $results = array_merge($results, $eventsTime);
            } else {
                foreach ($resultsDate as $resultDate) {
                    foreach ($eventsTime as $eventTime) {
                        if ($resultDate->getId() == $eventTime->getId()) {
                            $resultsTime[] = $eventTime;
                            $results[] = $eventTime;
                        }
                    }
                }
                $tempArray = [];
                foreach ($resultsTime as $eventTime) {
                    $tempArray[$eventTime->getId()] = $eventTime;
                }

                // Obtener los elementos únicos sin duplicados
                $resultsTime = array_values($tempArray);
                $results = array_values($tempArray);
            }
        }

        return $results;
    }

}