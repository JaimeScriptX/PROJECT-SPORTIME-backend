<?php

namespace App\Controller\Api;

use App\Entity\Sport;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\SportRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;

class SportController extends AbstractFOSRestController
{
    
    /**
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

                //get de las fotos de perfil con la url
                $getPhotoSport = $sport->getImage();
                $photoSport = $this->getParameter('url') . $getPhotoSport;

                $getLogo = $sport->getLogo();
                $Logo = $this->getParameter('url') . $getLogo;

                $data[] = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'image' => $photoSport,
                    'logo' => $Logo,
                ];

            }
            return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }

     /**
     * @Rest\Get(path="/sport/{id}")
     * @Rest\View(serializerGroups={"sport"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportById(EntityManagerInterface $em, int $id){

        $sport = $em->getRepository(Sport::class)->find($id);

        if(!$sport){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No sport found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }

                //get de las fotos de perfil con la url
                $getPhotoSport = $sport->getImage();
                $photoSport = $this->getParameter('url') . $getPhotoSport;

                $getLogo = $sport->getLogo();
                $Logo = $this->getParameter('url') . $getLogo;

                $data = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'image' => $photoSport,
                    'logo' => $Logo,
                ];

        return new JsonResponse($data, Response::HTTP_OK);
    
}

}
