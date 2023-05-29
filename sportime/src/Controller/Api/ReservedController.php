<?php

namespace App\Controller\Api;

use App\Entity\ReservedTime;
use App\Entity\SportCenter;
use App\Entity\ScheduleCenter;
use App\Entity\Events;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;  
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

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

        //Comprobación de que la fecha es correcta
        $date = new \DateTime($request->get('date'));
        $day = $date->format('N');

        $sportCenterSchedule = $entityManager->getRepository(ScheduleCenter::class)->findOneBy(['fk_sport_center_id' => $request->get('sportCenter'), 'day' => $day]);

        if (!$sportCenterSchedule) {
            return new JsonResponse(['status' => 'Sport center is closed on this day'], Response::HTTP_BAD_REQUEST);
        }
        else{
            //Comprobación de que la hora es correcta
            $start = $request->get('start');
            $end = $request->get('end');
            
            $sportCenterSchedules = $entityManager->getRepository(ScheduleCenter::class)->findBy(['fk_sport_center_id' => $request->get('sportCenter'), 'day' => $day]);
            
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
                $reservedTimes = $entityManager->getRepository(ReservedTime::class)->findBy(['fk_sport_center_id' => $request->get('sportCenter'), 'date' => $date]);

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
                    //Creación de la reserva
                   

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
     * @Rest\Get(path="/reservedTime/{sportCenter}/{date}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getReservedTimeAvailableBySportCenterAndDate($sportCenter, $date): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $day = (new \DateTime($date))->format('N');
        
        $sportCenterSchedules = $entityManager->getRepository(ScheduleCenter::class)->findBy(['fk_sport_center_id' => $sportCenter, 'day' => $day]);

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
                    if ($startSchedule <= $startReservedTime && $endSchedule >= $endReservedTime){
                        $reser = 1;
                    }
                }
                
                
                
            }

            //si la longitud de reservedTime es 0
            if ($reser==0) {
                $availableTime[] = [
                    'start_free' => $startSchedule,
                    'end_free' => $endSchedule,
                ];
            }else{
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

        return new JsonResponse($availableTime, Response::HTTP_OK);
    }
}
