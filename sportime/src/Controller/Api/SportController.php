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

class SportController extends AbstractFOSRestController
{
    
    /**
     * @OA\Tag(name="Sport")
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
     * @OA\Tag(name="Sport")
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

