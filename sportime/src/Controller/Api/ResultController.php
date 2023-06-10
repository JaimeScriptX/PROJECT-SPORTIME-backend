<?php

namespace App\Controller\Api;

use App\Entity\EventsResults;
use App\Entity\Events;
use App\Repository\EventsResultsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use LDAP\Result;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;

class ResultController extends AbstractFOSRestController
{

    /**
     * @OA\Tag(name="Result")
     * 
     * @Rest\Get(path="/result")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function getResults(EventsResultsRepository $eventsResultsRepository, EntityManagerInterface $entityManager)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $Results = $eventsResultsRepository->findAll();

        if (!$Results) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No Results found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $data = [];
            foreach ($Results as $Result) {

                $event = $Result->getFkEvent();

                $data[] = [
                    'id' => $Result->getId(),
                    'team_a' => $Result->getTeamA(),
                    'team_b' => $Result->getTeamB(),
                    'event' => [
                        'id' => $event->getId(),
                    ]
                ];
                
            }
            return new JsonResponse($data, Response::HTTP_OK);
        }
    
    }
    
    /**
     * @OA\Tag(name="Result")
     * 
     * @Rest\Get(path="/result/{id}")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function getResult(EventsResultsRepository $eventsResultsRepository, EntityManagerInterface $entityManager, $id)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $Result = $eventsResultsRepository->find($id);

        if (!$Result) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No Result found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $event = $Result->getFkEvent();

            $data = [
                'id' => $Result->getId(),
                'team_a' => $Result->getTeamA(),
                'team_b' => $Result->getTeamB(),
                'event' => [
                    'id' => $event->getId(),
                ]
            ];
            return new JsonResponse($data, Response::HTTP_OK);
        }
    
    }

   

/**
 * @OA\Tag(name="Result")
 * 
 * @Rest\Put(path="/result/{id}")
 * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
 */
public function putResult(Request $request, EventsResultsRepository $eventsResultsRepository, EntityManagerInterface $entityManager, $id)
{
    $teamA = $request->request->get('team_a');
    $teamB = $request->request->get('team_b');

    // Busca el resultado existente con el ID proporcionado
    $existingResult = $eventsResultsRepository->findOneBy(['fk_event' => $id]);

    if (!$existingResult) {
        return new JsonResponse(
            ['code' => 404, 'message' => 'No Result found for this ID.'],
            Response::HTTP_NOT_FOUND
        );
    }

  

    // Actualiza el resultado existente
    $existingResult->setTeamA($teamA);
    $existingResult->setTeamB($teamB);

    // Persiste los cambios en la base de datos
    $entityManager->flush();

    $event = $existingResult->getFkEvent();

    $data = [
        'id' => $existingResult->getId(),
        'team_a' => $existingResult->getTeamA(),
        'team_b' => $existingResult->getTeamB(),
        'event' => [
            'id' => $event->getId(),
        ]
    ];

    return new JsonResponse($data, Response::HTTP_OK);
}

 
    
}