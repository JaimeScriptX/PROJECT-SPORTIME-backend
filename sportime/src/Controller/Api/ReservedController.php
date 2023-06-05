<?php

namespace App\Controller\Api;

use App\Entity\ReservedTime;
use App\Entity\SportCenter;
use App\Entity\ScheduleCenter;
use App\Entity\Events;
use App\Entity\Sport;
use App\Entity\Difficulty;
use App\Entity\Sex;
use App\Entity\Person;
use App\Entity\State;
use App\Entity\TeamColor;
use App\Entity\EventPlayers as EntityEventPlayers;
use App\Entity\EventPlayers;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;  
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use DateTimeInterface;
use DateInterval;
use DatePeriod;

class ReservedController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/reservedTime")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReservedTime(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reservedTime = $entityManager->getRepository(ReservedTime::class)->findAll();

        $reservedTimeData = [];

        foreach ($reservedTime as $reservedTime) {
            $reservedTimeData[] = [
                'id' => $reservedTime->getId(),
                'date' => $reservedTime->getDate()->format('d-m-Y'),
                'start' => $reservedTime->getStart()->format('H:i'),
                'end' => $reservedTime->getEnd()->format('H:i'),
                'sportCenter' => [
                    'id' => $reservedTime->getFkSportCenterId()->getId(),
                    'name' => $reservedTime->getFkSportCenterId()->getName(),
                ],  
                'event' => [
                    'id' => $reservedTime->getFkEventId()->getId(),
                    'name' => $reservedTime->getFkEventId()->getName(),
                ],
                'isCanceled' => $reservedTime->isCanceled(),
                'cancellationReason' => $reservedTime->getCancellationReason() ? $reservedTime->getCancellationReason() : null,
            ];
        }

        return new JsonResponse($reservedTimeData, Response::HTTP_OK);
    }


    /**
     * @Rest\Get(path="/reservedTime/{id}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReservedTimeById($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reservedTime = $entityManager->getRepository(ReservedTime::class)->find($id);

        $reservedTimeData = [];

        $reservedTimeData[] = [
            'id' => $reservedTime->getId(),
            'date' => $reservedTime->getDate()->format('d-m-Y'),
            'start' => $reservedTime->getStart()->format('H:i'),
            'end' => $reservedTime->getEnd()->format('H:i'),
            'sportCenter' => [
                'id' => $reservedTime->getFkSportCenterId()->getId(),
                'name' => $reservedTime->getFkSportCenterId()->getName(),
            ],  
            'event' => [
                'id' => $reservedTime->getFkEventId()->getId(),
                'name' => $reservedTime->getFkEventId()->getName(),
            ],
            'isCanceled' => $reservedTime->isCanceled(),
            'cancellationReason' => $reservedTime->getCancellationReason() ? $reservedTime->getCancellationReason() : null,
        ];

        return new JsonResponse($reservedTimeData, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(path="/reservedTime/event/{id}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReservedTimeByEventId($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reservedTime = $entityManager->getRepository(ReservedTime::class)->findBy(['fk_event_id' => $id]);

        $reservedTimeData = [];

        foreach ($reservedTime as $reservedTime) {
            $reservedTimeData[] = [
                'id' => $reservedTime->getId(),
                'date' => $reservedTime->getDate()->format('d-m-Y'),
                'start' => $reservedTime->getStart()->format('H:i'),
                'end' => $reservedTime->getEnd()->format('H:i'),
                'sportCenter' => [
                    'id' => $reservedTime->getFkSportCenterId()->getId(),
                    'name' => $reservedTime->getFkSportCenterId()->getName(),
                ],  
                'event' => [
                    'id' => $reservedTime->getFkEventId()->getId(),
                    'name' => $reservedTime->getFkEventId()->getName(),
                    'sport' => [
                        'name' => $reservedTime->getFkEventId()->getFkSport()->getName(),
                    ],
                ],
                'isCanceled' => $reservedTime->isCanceled(),
                'cancellationReason' => $reservedTime->getCancellationReason() ? $reservedTime->getCancellationReason() : null,
            ];
        }

        return new JsonResponse($reservedTimeData, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="/reservedTime")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function postReservedTime(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        //Comprobación de que la fecha es correcta
        $date = new DateTime( $data['date']);
        $day = $date->format('N');

        $sportCenterSchedule = $entityManager->getRepository(ScheduleCenter::class)->findOneBy(['fk_sport_center_id' => $data['sportCenter'], 'day' => $day]);

        if (!$sportCenterSchedule) {
            return new JsonResponse(['status' => 'Sport center is closed on this day'], Response::HTTP_BAD_REQUEST);
        }
        else{
            //Comprobación de que la hora es correcta
            $start = $data['start'];
            $end = $data['end'];

            //$end = date('H:i:s', strtotime($start. ' + 1 hour'));
            
            $sportCenterSchedules = $entityManager->getRepository(ScheduleCenter::class)->findBy(['fk_sport_center_id' => $data['sportCenter'], 'day' => $day]);
            
            $isCorrectTime = false;
            
            foreach ($sportCenterSchedules as $sportCenterSchedule) {
                //formato horas H:i:s
                $startSchedule = $sportCenterSchedule->getStart()->format('H:i:s');
                $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');
            
                if ($start >= $startSchedule && $end <= $endSchedule) {
                    $isCorrectTime = true;
                }
            }

            if (!$isCorrectTime) {
                return new JsonResponse(['status' => 'Sport center is closed on this time'], Response::HTTP_BAD_REQUEST);
            }
            else{
                //Comprobación de que no colisiona con otra reserva que no esté cancelada
                $reservedTimes = $entityManager->getRepository(ReservedTime::class)->findBy(['fk_sport_center_id' => $data['sportCenter'], 'date' => $date]);

                $isCorrectTime = true;

                foreach ($reservedTimes as $reservedTime) {
                    //formato horas H:i:s
                    $startReservedTime = $reservedTime->getStart()->format('H:i:s');
                    $endReservedTime = $reservedTime->getEnd()->format('H:i:s');
                    
                    //no tiene que existir reservas entre las horas start y end

                    if ($start > $startReservedTime && $end < $endReservedTime) {
                        if (!$reservedTime->isCanceled()) {
                            $isCorrectTime = false;
                            break;
                        }
                    } 
                    if ($start < $startReservedTime && $end > $endReservedTime) {
                            if (!$reservedTime->isCanceled()) {
                            $isCorrectTime = false;
                            break;
                        }
                    }

                    //no puede empezar una reserva entre las horas start y end
                    if ($start > $startReservedTime && $start < $endReservedTime) {
                        if (!$reservedTime->isCanceled()) {
                            $isCorrectTime = false;
                            break;
                        }
                    }

                    //no puede acabar una reserva entre las horas start y end
                    if ($end > $startReservedTime && $end < $endReservedTime) {
                        if (!$reservedTime->isCanceled()) {
                            $isCorrectTime = false;
                            break;
                        }
                    }
                    
                }

                if (!$isCorrectTime) {
                    return new JsonResponse(['status' => 'Sport center is reserved on this time'], Response::HTTP_BAD_REQUEST);
                }
                
               else{
                //si start y end duran 1 hora y si duran más de 1 hora $end se le suma 1 hora
                $durationSE = strtotime($end) - strtotime($start);
                $durationSE = date('H:i:s', $durationSE);
                $duration = new DateTime($durationSE);

                if ($duration->format('H') > 1) {
                    $end2 = date('H:i:s', strtotime($end. ' + 1 hour'));
                    $end = new DateTime($end2);

                }

                   //Creación del evento
                  $events = new Events();
                  $events->setName($data['name']);
                  $events->setIsPrivate($data['is_private']);
                  $events->setDetails($data['details']);
                  $events->setPrice($data['price']);
                  $events->setDate(new \DateTime($data['date']));
                  $events->setTime(new \DateTime($data['start']));
                  $events->setNumberPlayers($data['number_players']);
                  $events->setDuration($duration);
                  
                  // fk
                  $sport = $entityManager->getRepository(Sport::class)->findOneBy(['name' => $data['fk_sport']]);
                  $events->setFkSport($sport);

                    $sportCenter = $entityManager->getRepository(SportCenter::class)->findOneBy(['name' => $data['fk_sportcenter']]);
                    $events->setFkSportcenter($sportCenter);

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


                 $eventPlayer = new EventPlayers();
                 $event = $entityManager->getRepository(Events::class)->find(['id' => $events->getId()]);
                 $eventPlayer->setFkEvent($event);
         
                 $person = $entityManager->getRepository(Person::class)->find(['id' => $data['fk_person']]);
                 $eventPlayer->setFkPerson($person);
         
                 $eventPlayer->setEquipo(1);
         
                 $entityManager->persist($eventPlayer);
                 $entityManager->flush();

                   //Creación de la reserva
                   $reser = new ReservedTime();
                   $reser->setDay($day);
                   $reser->setDate($date);
                   $reser->setStart(new \DateTime($start));
                   $reser->setEnd(new \DateTime($end));
                   $reser->setDateCreated($date);
                   $reser->setCanceled(false);
                   
                   //fk
                   $rsportCenter = $entityManager->getRepository(SportCenter::class)->findOneBy(['id' => $data['sportCenter']]);
                   $reser->setFkSportCenterId($rsportCenter);
                   
                  $revent = $entityManager->getRepository(Events::class)->findOneBy(['name' => $data['name']]);
                  $reser->setFkEventId($revent);


                   $entityManager->persist($reser);
                   $entityManager->flush();

                   
                   return new JsonResponse(['status' => 'Reserved time created!'], Response::HTTP_CREATED);
               }
            }
        }

        
        



     /*  
        //Creación de la reserva
        $reservedTime = new ReservedTime();

        $reservedTime->setDate(new \DateTime($request->get('date')));
        $reservedTime->setStart(new \DateTime($request->get('start')));
        $reservedTime->setEnd(new \DateTime($request->get('end')));
        $reservedTime->setFkSportCenterId($entityManager->getRepository(SportCenter::class)->find($request->get('sportCenter')));
        $reservedTime->setFkEventId($entityManager->getRepository(Events::class)->find($request->get('event')));
        $reservedTime->setCanceled(false);

        $entityManager->persist($reservedTime);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Reserved time created!'], Response::HTTP_CREATED);
        */
    }


    /**
     * @Rest\Get(path="/reservedTime/{sportCenter}/{date}/{sport}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReservedTimeAvailableBySportCenterAndDate($sportCenter, $date, $sport): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $day = (new \DateTime($date))->format('N');
        
        $sportCenterSchedules = $entityManager->getRepository(ScheduleCenter::class)->findBy(['fk_sport_center_id' => $sportCenter, 'day' => $day]);
        if (!$sportCenterSchedules) {
            return new JsonResponse(['status' => 'Sport center is closed'], Response::HTTP_NOT_FOUND);
        }
        $availableTime = [];

        foreach ($sportCenterSchedules as $sportCenterSchedule) {
            $reser=0;
            $reservedTimes = $entityManager->getRepository(ReservedTime::class)->findBy(['fk_sport_center_id' => $sportCenter, 'date' => new \DateTime($date)]);
            $startSchedule = $sportCenterSchedule->getStart()->format('H:i:s');
            $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');
            $startScheduleFin = $sportCenterSchedule->getStart()->format('H:i:s');
            $endScheduleFin = $sportCenterSchedule->getEnd()->format('H:i:s');
            
            foreach ($reservedTimes as $reservedTime){
                $startReservedTime = $reservedTime->getStart()->format('H:i:s');
                $endReservedTime = $reservedTime->getEnd()->format('H:i:s'); 
                $isCanceled = $reservedTime->isCanceled();
                
                if(!$isCanceled){
                    if ($startSchedule <= $startReservedTime && $endSchedule >= $endReservedTime && $reservedTime->getFkEventId()->getFkSport()->getId() == $sport){
                        $reser = 1;
                    }
                }
                
                
                
            }
            $AllTimes[] = [
                'start_free' => $startSchedule,
                'end_free' => $endSchedule,
            ];
            //si la longitud de reservedTime es 0
            if ($reser==0) {
                $availableTime[] = [
                    'start_free' => $startSchedule,
                    'end_free' => $endSchedule,
                ];
            }else{
                //filtrar reservedTimes para que solo salgan las que tengan el sport id que se quiere en su evento
                $reservedTimes = array_filter($reservedTimes, function($reservedTime) use ($sport) {
                    return $reservedTime->getFkEventId()->getFkSport()->getId() == $sport;
                });
                

                //ordenar reservedTimes por hora de start de menor a mayor
                usort($reservedTimes, function($a, $b) {
                    return $a->getStart() <=> $b->getStart();
                });
                
                $startFree = 0;
                for ($i=0; $i < count($reservedTimes); $i++) {

                    $startReservedTime = $reservedTimes[$i]->getStart()->format('H:i:s');
                    $endReservedTime = $reservedTimes[$i]->getEnd()->format('H:i:s'); 
                    $isCanceled = $reservedTimes[$i]->isCanceled();
                    
                    if ($startScheduleFin <= $startReservedTime && $endReservedTime >= $endReservedTime){
                        if ($isCanceled){
                        continue;
                    }
                    
                    if ($startSchedule == $startReservedTime) {
                        $startSchedule = $endReservedTime;
                        if (isset($reservedTimes[$i + 1])) {
                            $isCanceled = $reservedTimes[$i+1]->isCanceled();
                            if ($isCanceled){
                                $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');
                            }else{
                                $endSchedule = $reservedTimes[$i + 1]->getStart()->format('H:i:s');
                            }
                            

                        } else {
                            $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');

                        }
                        
                    }else{
                    
                    
                        $endSchedule = $startReservedTime;
                    }
                        
                    //Paint 1
                    if ($startSchedule != $sportCenterSchedule->getEnd()->format('H:i:s')) {
                        if ($startFree != $startSchedule){
                            if ($startSchedule != $endSchedule && $startSchedule < $endSchedule){
                                $availableTime[] = [
                                'start_free' => $startSchedule,
                                'end_free' => $endSchedule,
                            ];
                            }
                            else{
                                $availableTime[] = [
                                'start_free' => 0,
                                'end_free' => 0,
                            ];
                            }
                        }
                            
                    }
                    $lastElement = end($availableTime);
                    $startFree = $lastElement['start_free'];
                    
                    $startSchedule = $endReservedTime;
                    
                    if (isset($reservedTimes[$i + 1])) {
                            $isCanceled = $reservedTimes[$i+1]->isCanceled();
                            if ($isCanceled){
                                $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');
                            }else{
                                $endSchedule = $reservedTimes[$i + 1]->getStart()->format('H:i:s');
                            }
                            

                        } else {
                            $endSchedule = $sportCenterSchedule->getEnd()->format('H:i:s');

                        }
                    
                    //Paint 2
                    if ($startSchedule != $sportCenterSchedule->getEnd()->format('H:i:s')) {
                        if ($startFree != $startSchedule){
                            if ($startSchedule != $endSchedule && $startSchedule < $endSchedule){
                                $availableTime[] = [
                                'start_free' => $startSchedule,
                                'end_free' => $endSchedule,
                            ];
                            }
                            else{
                                $availableTime[] = [
                                'start_free' => 0,
                                'end_free' => 0,
                            ];
                            }
                        }
                        
                    }
                    $lastElement = end($availableTime);
                    $startFree = $lastElement['start_free'];
                    
                    
                    }
                    
                    
                }
            }

            
        }

        $temp=[];
        
        foreach ($availableTime as $availableTim){
            if ($availableTim['start_free']!=0){
                $temp[]= $availableTim;
            }
        }
        $availableTime = $temp;

        $availableTimes = [];

        foreach ($AllTimes as $centerTime) {
            $start = new DateTime($centerTime['start_free']);
            $end = new DateTime($centerTime['end_free']);
            $interval = new DateInterval('PT1H');
            $period = new DatePeriod($start, $interval, $end);
        
            foreach ($period as $time) {
                $formattedTime = $time->format('H:i:s');
                $isAvailable = false;
            
                foreach ($availableTime as $available) {
                    $startTime = new DateTime($available['start_free']);
                    $endTime = new DateTime($available['end_free']);
                
                    if ($time >= $startTime && $time < $endTime) {
                        $isAvailable = true;
                        break; // Salir del bucle si encontramos una coincidencia
                    }
                }
            
                $availableTimes[] = [
                    'start_free' => $formattedTime,
                    //'end_free' => $time->add($interval)->format('H:i:s'),
                    'isAvailable' => $isAvailable,
                ];
            }
        }

        //divideme $availableTimes en mañana y tarde
        $morning = [];
        $afternoon = [];
        $night = [];
        
        foreach ($availableTimes as $availableTime) {
            $start = new DateTime($availableTime['start_free']);
            $end = new DateTime($availableTime['start_free']);
            $end->add(new DateInterval('PT1H'));
            $actualTime = new DateTime();

            //solo pintar las horas que no hayan pasado
            if ($start < $actualTime) {
                continue;
            }
            

            if ($start->format('H') <= 12) {
                $morning[] = [
                    'hour' => $start->format('H:i'),
                    //'end_free' => $end->format('H:i:s'),
                    'isAvailable' => $availableTime['isAvailable'],
                ];
            } elseif ($start->format('H') < 20) {
                $afternoon[] = [
                    'hour' => $start->format('H:i'),
                    //'end_free' => $end->format('H:i:s'),
                    'isAvailable' => $availableTime['isAvailable'],
                ];
            } elseif ($start->format('H') >= 20 && $start->format('H') < 23) {
                $night[] = [
                    'hour' => $start->format('H:i'),
                    //'end_free' => $end->format('H:i:s'),
                    'isAvailable' => $availableTime['isAvailable'],
                ];
            }
        }
        
        $availableTime = [
            'morning' => $morning,
            'afternoon' => $afternoon,
            'night' => $night,
        ];

        if (empty($availableTime)) {
            return new JsonResponse(['error' => 'There are no available times'], Response::HTTP_NOT_FOUND);
        }
        else{
            return new JsonResponse($availableTime, Response::HTTP_OK);
        }
    }


    /**
     * @Rest\Delete(path="/reservedTime/{id}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function cancelReservation($id, Request $request): Response
    {

        $event = $this->getDoctrine()->getRepository(Events::class)->findOneBy(['id' => $id]);
        $reservation = $this->getDoctrine()->getRepository(ReservedTime::class)->findOneBy(['fk_event_id' => $event->getId()]);
        $reservation->setCanceled(true);
        $event = $this->getDoctrine()->getRepository(Events::class)->findOneBy(['id' => $reservation->getFkEventId()]);
        $state= $this->getDoctrine()->getRepository(State::class)->findOneBy(['id' => 5]);
        $event->setFkState($state);
        $entityManager = $this->getDoctrine()->getManager();

        $cancellationReason = $request->get('cancellationReason');
        if (empty($cancellationReason)) {
            $cancellationReason = 'Sin especificar';
        }
        else{
            $reservation->setCancellationReason($cancellationReason);
        }
        

        $entityManager->persist($reservation);
        $entityManager->persist($event);
        $entityManager->flush();
        return new JsonResponse(['status' => 'Reservation canceled'], Response::HTTP_OK);
    }
}
