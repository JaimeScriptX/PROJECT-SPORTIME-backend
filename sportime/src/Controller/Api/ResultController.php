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
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;

class ResultController extends AbstractFOSRestController
{

    /**
     * getResults
     * 
     * Get all results
     * 
     * @OA\Tag(name="Result")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns all results",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="team_a", type="string", example="1"),
     *  @OA\Property(property="team_b", type="string", example="1"),
     *  @OA\Property(property="event", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  ))
     *  )
     *  )
     * 
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
     * getResult
     * 
     * Get result by id
     * 
     * @OA\Tag(name="Result")
     * 
     * @OA\Response(
     *  response=200,
     *  description="Returns result by id",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="team_a", type="string", example="1"),
     *  @OA\Property(property="team_b", type="string", example="1"),
     *  @OA\Property(property="event", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  ))
     *  )
     *  )
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
 * putResult
 * 
 * Update result by id
 * 
 * @OA\Tag(name="Result")
 * 
 * @OA\RequestBody(
 *  request="Result",
 *  required=true,
 *  description="Update result by id",
 *  @OA\JsonContent(
 *  @OA\Property(property="team_a", type="string", example="1"),
 *  @OA\Property(property="team_b", type="string", example="1"),
 *  )
 *  )
 *  )
 * 
 *  @OA\Response(
 *  response=200,
 *  description="Returns result updated",
 *  @OA\JsonContent(
 *  @OA\Property(property="id", type="string", example="1"),
 *  @OA\Property(property="team_a", type="string", example="1"),
 *  @OA\Property(property="team_b", type="string", example="1"),
 *  @OA\Property(property="event", type="array", @OA\Items(
 *  @OA\Property(property="id", type="string", example="1"),
 *  ))
 *  )
 *  )
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