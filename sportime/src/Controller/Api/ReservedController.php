<?php

namespace App\Controller\Api;

use App\Entity\ReservedTime;
use App\Entity\SportCenter;
use App\Entity\SportCenterSchedule;
use App\Entity\Events;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;  
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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
}