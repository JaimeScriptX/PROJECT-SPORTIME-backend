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
    public function getSportCenter(SportCenterRepository $sportCenterRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sportCenters = $entityManager->getRepository(SportCenter::class)->findAll();

        //devuelve servicios
        $sportCenterData = [];

        foreach ($sportCenters as $sportCenter) {
            $services = $sportCenter->getFkServices();
            $servicesData = [];

            foreach ($services as $service) {
                $servicesData[] = [
                    'id' => $service->getId(),
                    'name' => $service->getName(),
                ];
            }

            $sport = $sportCenter->getFkSport();
            $sportData = [];
    
            foreach ($sport as $sport) {
    
                //get imagesport
                $getPhotoSport = $sport->getImage();
                $photoSport = $this->getParameter('url') . $getPhotoSport;
    
                //get logo sportcenter
                $getLogoSportCenter = $sport->getLogoSportCenter();
                $LogoSportCenter = $this->getParameter('url') . $getLogoSportCenter;
    
                $sportData[] = [
                    'id' => $sport->getId(),
                    'name' => $sport->getName(),
                    'image' => $photoSport,
                    'logo_sportcenter' =>  $LogoSportCenter,
                ];
            }

            //get image
            $getImage = $sportCenter->getImage();
            $image = $this->getParameter('url') . $getImage;

            //get gallery1
            $getGallery1 = $sportCenter->getImageGallery1();
            $gallery1 = $this->getParameter('url') . $getGallery1;

            //get gallery2
            $getGallery2 = $sportCenter->getImageGallery2();
            $Gallery2 = $this->getParameter('url') . $getGallery2;

            //get gallery3
            $getGallery3 = $sportCenter->getImageGallery3();
            $Gallery3 = $this->getParameter('url') . $getGallery3;

            //get gallery4
            $getGallery4 = $sportCenter->getImageGallery4();
            $Gallery4 = $this->getParameter('url') . $getGallery4;


            $sportCenterData[] = [
                'id' => $sportCenter->getId(),
                'name' => $sportCenter->getName(),
                'municipality' => $sportCenter->getMunicipality(),
                'address' => $sportCenter->getAddress(),
                'image' => $image,
                'phone' => $sportCenter->getPhone(),
                "gallery" => [
                    (object) ['image_gallery1' => $gallery1],
                    (object) ['image_gallery2' => $Gallery2],
                    (object) ['image_gallery3' => $Gallery3],
                    (object) ['image_gallery4' => $Gallery4],
                ],
                'latitude' => $sportCenter->getLatitude(),
                'longitude' => $sportCenter->getLongitude(),
                'destination' => $sportCenter->getDestination(),
                'services' => $servicesData,
                'sport' => $sportData,
            ];
        }

        return $this->json($sportCenterData);
    }
        
    

    /**
     * @Rest\Get(path="/sportcenter/{id}")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportCenterById($id)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $sportCenter = $entityManager->getRepository(SportCenter::class)->find($id);

        if (!$sportCenter) {
            throw $this->createNotFoundException('SportCenter not found');
        }

        //Devuelve los servicios
        $services = $sportCenter->getFkServices();
        $servicesData = [];

        foreach ($services as $service) {
            $servicesData[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
              
            ];
        }

        //Devuelve los deportes
        $sport = $sportCenter->getFkSport();
        $sportData = [];

        foreach ($sport as $sport) {

            //get imagesport
            $getPhotoSport = $sport->getImage();
            $photoSport = $this->getParameter('url') . $getPhotoSport;

            //get logo sportcenter
            $getLogoSportCenter = $sport->getLogoSportCenter();
            $LogoSportCenter = $this->getParameter('url') . $getLogoSportCenter;

            $sportData[] = [
                'id' => $sport->getId(),
                'name' => $sport->getName(),
                'image' => $photoSport,
                'logo_sportcenter' =>  $LogoSportCenter,
            ];
        }

        //get image
        $getImage = $sportCenter->getImage();
        $image = $this->getParameter('url') . $getImage;

        //get gallery1
        $getGallery1 = $sportCenter->getImageGallery1();
        $gallery1 = $this->getParameter('url') . $getGallery1;

        //get gallery2
        $getGallery2 = $sportCenter->getImageGallery2();
        $Gallery2 = $this->getParameter('url') . $getGallery2;

        //get gallery3
        $getGallery3 = $sportCenter->getImageGallery3();
        $Gallery3 = $this->getParameter('url') . $getGallery3;

        //get gallery4
        $getGallery4 = $sportCenter->getImageGallery4();
        $Gallery4 = $this->getParameter('url') . $getGallery4;


        $response = [
            'id' => $sportCenter->getId(),
            'name' => $sportCenter->getName(),
            'municipality' => $sportCenter->getMunicipality(),
            'address' => $sportCenter->getAddress(),
            'image' => $image,
            'phone' => $sportCenter->getPhone(),
            "gallery" => [
                (object) ['image_gallery1' => $gallery1],
                (object) ['image_gallery2' => $Gallery2],
                (object) ['image_gallery3' => $Gallery3],
                (object) ['image_gallery4' => $Gallery4],
            ],
            'latitude' => $sportCenter->getLatitude(),
            'longitude' => $sportCenter->getLongitude(),
            'destination' => $sportCenter->getDestination(),
            'services' => $servicesData,
            'sport' => $sportData,
        ];

        return $this->json($response);
       
        
    }

   
}