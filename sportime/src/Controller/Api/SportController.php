<?php

namespace App\Controller\Api;

use App\Entity\Sport;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\SportRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;

class SportController extends AbstractFOSRestController
{
    
    /**
     * getSport
     * 
     * Get all sports
     * 
     * @OA\Tag(name="Sport")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns all sports",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="Football"),
     *  @OA\Property(property="image", type="string", example="/football.png"),
     *  @OA\Property(property="logo_event", type="string", example="/football_logo1.png"),
     *  @OA\Property(property="logo_sportcenter", type="string", example="/football_logo2.png"),
     *  )
     *  )
     * 
     * @Rest\Get(path="/sport")
     * @Rest\View(serializerGroups={"sport"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSport(SportRepository $sportRepository)
    {
        $sports = $sportRepository->findAll();

        if(!$sports){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No persons found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }else{

            $data = [];
            foreach($sports as $sport){

                //get imagesport
                $getPhotoSport = $sport->getImage();
                $photoSport = $this->getParameter('url') . $getPhotoSport;

                //get logo event
                $getLogoEvent = $sport->getLogoEvent();
                $LogoEvent = $this->getParameter('url') . $getLogoEvent;

                //get logo sportcenter
                $getLogoSportCenter = $sport->getLogoSportCenter();
                $LogoSportCenter = $this->getParameter('url') . $getLogoSportCenter;

                $data[] = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'image' => $photoSport,
                    'logo_event' => $LogoEvent,
                    'logo_sportcenter' => $LogoSportCenter,
                ];

            }
            return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }

    /**
     * getSportById
     * 
     * Get sport by id
     * 
     * @OA\Tag(name="Sport")
     * 
     * @OA\Response(
     *  response=200,
     *  description="Returns sport by id",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="Football"),
     *  @OA\Property(property="image", type="string", example="/football.png"),
     *  @OA\Property(property="logo_event", type="string", example="/football_logo1.png"),
     *  @OA\Property(property="logo_sportcenter", type="string", example="/football_logo2.png"),
     *  )
     *  )
     * 
     * @Rest\Get(path="/sport/{id}")
     * @Rest\View(serializerGroups={"sport"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportById(EntityManagerInterface $em, $id){

        $sport = $em->getRepository(Sport::class)->find($id);

        if(!$sport){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No sport found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }

        if ($sport === null){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NOT_FOUND
            );
        }

                //get imagesport
                $getPhotoSport = $sport->getImage();
                $photoSport = $this->getParameter('url') . $getPhotoSport;

                //get logo event
                $getLogoEvent = $sport->getLogoEvent();
                $LogoEvent = $this->getParameter('url') . $getLogoEvent;

                //get logo sportcenter
                $getLogoSportCenter = $sport->getLogoSportCenter();
                $LogoSportCenter = $this->getParameter('url') . $getLogoSportCenter;


                $data = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'image' => $photoSport,
                    'logo_event' => $LogoEvent,
                    'logo_sportcenter' => $LogoSportCenter,
                ];

        return new JsonResponse($data, Response::HTTP_OK);
    
}

}

