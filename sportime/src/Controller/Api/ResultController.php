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

class ResultController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(path="/result")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function getResults(EventsResultsRepository $eventsResultsRepository, EntityManagerInterface $entityManager)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $eventsResults = $eventsResultsRepository->findAll();

        if (!$eventsResults) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No results found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $data = [];
            foreach ($eventsResults as $eventsResult) {

                $data[] = [
                    'id' => $eventsResult->getId(),
                    'team_a' => $eventsResult->getTeamA(),
                    'team_b' => $eventsResult->getTeamB(),
                ];
            }
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Get(path="/result/{id}")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function getResult(EventsResultsRepository $eventsResultsRepository, EntityManagerInterface $entityManager, $id)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $eventsResult = $eventsResultsRepository->find($id);

        if (!$eventsResult) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No result found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $data = [
                'id' => $eventsResult->getId(),
                'team_a' => $eventsResult->getTeamA(),
                'team_b' => $eventsResult->getTeamB(),
            ];
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Post(path="/result")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function postResult(Request $request, EntityManagerInterface $entityManager)
    {
        $eventsResult = new EventsResults();

        $eventsResult->setTeamA($request->get('team_a'));
        $eventsResult->setTeamB($request->get('team_b'));

        $entityManager->persist($eventsResult);
        $entityManager->flush();

        $data = [
            'id' => $eventsResult->getId(),
            'team_a' => $eventsResult->getTeamA(),
            'team_b' => $eventsResult->getTeamB(),
        ];

        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(path="/result/{id}")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function putResult(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $eventsResult = $eventsResultsRepository->find($id);

        if (!$eventsResult) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No result found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $eventsResult->setTeamA($request->get('team_a'));
            $eventsResult->setTeamB($request->get('team_b'));

            $entityManager->persist($eventsResult);
            $entityManager->flush();

            $data = [
                'id' => $eventsResult->getId(),
                'team_a' => $eventsResult->getTeamA(),
                'team_b' => $eventsResult->getTeamB(),
            ];

            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Delete(path="/result/{id}")
     * @Rest\View(serializerGroups={"EventsResult"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteResult(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $eventsResultsRepository = $entityManager->getRepository(EventsResults::class);
        $eventsResult = $eventsResultsRepository->find($id);

        if (!$eventsResult) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No result found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {

            $entityManager->remove($eventsResult);
            $entityManager->flush();

            return new JsonResponse(
                ['code' => 200, 'message' => 'Result deleted successfully.'],
                Response::HTTP_OK
            );
        }
    }
        

}