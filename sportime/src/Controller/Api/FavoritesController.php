<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Difficulty;
use App\Entity\EventPlayers;
use App\Repository\EventsRepository;
use App\Entity\Favorites;
use App\Repository\FavoritesRepository;
use App\Entity\Sport;
use App\Entity\Sex;
use App\Service\EventsManager;
use Doctrine\ORM\EntityManagerInterface;
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
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\Event;
use OpenApi\Annotations as OA;

class FavoritesController extends AbstractFOSRestController
{
    /**
     * @OA\Tag(name="Favorites")
     * 
     * @Rest\Get(path="/favorites/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */

     public function getFavoritesById(
        EntityManagerInterface $entityManager,
        $id
    ){
        $favoritesRepository = $entityManager->getRepository(Favorites::class);
        $favorites = $favoritesRepository->findBy(['fk_person' => $id]);

        if (!$favorites) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No favorites found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = [];
            foreach ($favorites as $favorite) {
                $data[] = [
                    'id' => $favorite->getId(),
                    'fk_person_id' => $favorite->getFkPerson()->getId(),
                    'fk_sport_id' => $favorite->getFkSport()->getId(),
                    'sport_name' => $favorite->getFkSport()->getName(),
                    'sport_image' => $this->getParameter('url') . $favorite->getFkSport()->getImage(),
                ];
            }
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    /**
     * @OA\Tag(name="Favorites")
     * 
     * @Rest\Post(path="/favorites")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function postFavorites(
        EntityManagerInterface $entityManager,
        Request $request
    ){
        $data = json_decode($request->getContent(), true);
        $personId = $data['fk_person_id'];
        $sportId = $data['fk_sport_id'];

        $favoritesRepository = $entityManager->getRepository(Favorites::class);
        $favorites = $favoritesRepository->findOneBy(['fk_person' => $personId, 'fk_sport' => $sportId]);

        if (!$favorites) {
            $favorites = new Favorites();
            $favorites->setFkPerson($entityManager->getRepository(Person::class)->find($personId));
            $favorites->setFkSport($entityManager->getRepository(Sport::class)->find($sportId));
            $entityManager->persist($favorites);
            $entityManager->flush();
            return new JsonResponse(
                ['code' => 200, 'message' => 'Favorite created successfully.'],
                Response::HTTP_OK
            );
        } else {
            return new JsonResponse(
                ['code' => 400, 'message' => 'Favorite already exists.'],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @OA\Tag(name="Favorites")
     * 
     * @Rest\Delete(path="/favorites/{id}")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteFavorites(
        EntityManagerInterface $entityManager,
        $id
    ){
        $favoritesRepository = $entityManager->getRepository(Favorites::class);
        $favorites = $favoritesRepository->find($id);

        if (!$favorites) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No favorites found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $entityManager->remove($favorites);
            $entityManager->flush();
            return new JsonResponse(
                ['code' => 200, 'message' => 'Favorite deleted successfully.'],
                Response::HTTP_OK
            );
        }
    }

}