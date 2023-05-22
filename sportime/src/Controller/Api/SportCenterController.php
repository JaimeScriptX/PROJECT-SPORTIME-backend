<?php

namespace App\Controller\Api;

use App\Entity\SportCenter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\SportCenterRepository;
use Symfony\Component\HttpFoundation\JsonResponse;  
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;

class SportCenterController extends AbstractFOSRestController
{
    
    /**
     * @Rest\Get(path="/sportcenter")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportCenter(SportCenterRepository $sportCenterRepository)
    {
        $sportCenters = $sportCenterRepository->findAll();

        if(!$sportCenters){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No persons found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }else{

            $data = [];
            foreach($sportCenters as $sportCenter){

                //get image
                $getImage = $sportCenter->getImage();
                $image = $this->getParameter('url') . $getImage;

                $data[] = [
                    'id' => $sportCenter->getId(),
                    'name' => $sportCenter->getName(),
                    'municipality' => $sportCenter->getMunicipality(),
                    'address' => $sportCenter->getAddress(),
                    'image' => $image,
                    'phone' => $sportCenter->getPhone(),
                ];

            }
            return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }

    /**
     * @Rest\Get(path="/sportcenter/{id}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportCenterById(SportCenterRepository $sportCenterRepository, $id)
    {
        $sportCenter = $sportCenterRepository->find($id);

        if(!$sportCenter){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No persons found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        }else{

            //get image
            $getImage = $sportCenter->getImage();
            $image = $this->getParameter('url') . $getImage;

            $data = [
                'id' => $sportCenter->getId(),
                'name' => $sportCenter->getName(),
                'municipality' => $sportCenter->getMunicipality(),
                'address' => $sportCenter->getAddress(),
                'image' => $image,
                'phone' => $sportCenter->getPhone(),
            ];

            return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }

   
}