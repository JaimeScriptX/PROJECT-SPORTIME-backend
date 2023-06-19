<?php

namespace App\Controller\Api;

use App\Entity\SportCenter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\SportCenterRepository;
use Symfony\Component\HttpFoundation\JsonResponse;  
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;

class SportCenterController extends AbstractFOSRestController
{
    
    /**
     * getSportCenter
     * 
     * Get all sportcenter
     * 
     * @OA\Tag(name="SportCenter")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns all sportcenter",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  @OA\Property(property="address", type="string", example="1"),
     *  @OA\Property(property="phone", type="string", example="1"),
     *  @OA\Property(property="email", type="string", example="1"),
     *  @OA\Property(property="gallery", type="array", @OA\Items(
     *  @OA\Property(property="image_gallery1", type="object", example="1"),
     *  @OA\Property(property="image_gallery2", type="object", example="1"),
     *  @OA\Property(property="image_gallery3", type="object", example="1"),
     *  @OA\Property(property="image_gallery4", type="object", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="latitude", type="string", example="1"),
     *  @OA\Property(property="longitude", type="string", example="1"),
     *  @OA\Property(property="destination", type="string", example="Street"),
     *  @OA\Property(property="sport", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="services", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="schedule", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="day", type="string", example="1"),
     *  @OA\Property(property="opening_time", type="string", example="1"),
     *  @OA\Property(property="closing_time", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="description", type="string", example="Description"),
     *  @OA\Property(property="price", type="decimal", example="1"),
     *  )
     *  )
     * 
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
            
            //sportCenterSchedule
            $sportCenterSchedule = $sportCenter->getScheduleCenters();
            $sportCenterScheduleData = [];

            foreach ($sportCenterSchedule as $sportCenterSchedule) {
                $sportCenterScheduleData[] = [
                    'id' => $sportCenterSchedule->getId(),
                    'day' => $sportCenterSchedule->getDay(),
                    'opening_time' => $sportCenterSchedule->getStart()->format('H:i'),
                    'closing_time' => $sportCenterSchedule->getEnd()->format('H:i'),
                ];
            }

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
                'schedule' => $sportCenterScheduleData,
                'description' => $sportCenter->getDescription() ? $sportCenter->getDescription() : null,
                'price' => $sportCenter->getPrice(),
            ];
        }

        return $this->json($sportCenterData);
    }
        
    

    /**
     * getSportCenterById
     * 
     * Get sportcenter by id.
     * 
     * @OA\Tag(name="SportCenter")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns all sportcenter",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  @OA\Property(property="address", type="string", example="1"),
     *  @OA\Property(property="phone", type="string", example="1"),
     *  @OA\Property(property="email", type="string", example="1"),
     *  @OA\Property(property="gallery", type="array", @OA\Items(
     *  @OA\Property(property="image_gallery1", type="object", example="1"),
     *  @OA\Property(property="image_gallery2", type="object", example="1"),
     *  @OA\Property(property="image_gallery3", type="object", example="1"),
     *  @OA\Property(property="image_gallery4", type="object", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="latitude", type="string", example="1"),
     *  @OA\Property(property="longitude", type="string", example="1"),
     *  @OA\Property(property="destination", type="string", example="Street"),
     *  @OA\Property(property="sport", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="services", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="schedule", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="day", type="string", example="1"),
     *  @OA\Property(property="opening_time", type="string", example="1"),
     *  @OA\Property(property="closing_time", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="description", type="string", example="Description"),
     *  @OA\Property(property="price", type="decimal", example="1"),
     *  )
     *  )
     * 
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

        if ($sportCenter === null){
            return new JsonResponse(
                ['code' => 204, 'message' => 'No person found for this query.'],
                Response::HTTP_NOT_FOUND
            );
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

        //sportCenterSchedule
        $sportCenterSchedule = $sportCenter->getScheduleCenters();
        $sportCenterScheduleData = [];

        foreach ($sportCenterSchedule as $sportCenterSchedule) {
            $sportCenterScheduleData[] = [
                'id' => $sportCenterSchedule->getId(),
                'day' => $sportCenterSchedule->getDay(),
                'opening_time' => $sportCenterSchedule->getStart()->format('H:i'),
                'closing_time' => $sportCenterSchedule->getEnd()->format('H:i'),
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
            'schedule' => $sportCenterScheduleData,
            'description' => $sportCenter->getDescription() ? $sportCenter->getDescription() : null,
            'price' => $sportCenter->getPrice(),
        ];

        return $this->json($response);
       
        
    }

    /**
     * getSportCenterSport
     * 
     * Get sportcenter by sport id.
     * 
     * @OA\Tag(name="SportCenter")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns all sportcenter",
     *  @OA\JsonContent(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  @OA\Property(property="address", type="string", example="1"),
     *  @OA\Property(property="phone", type="string", example="1"),
     *  @OA\Property(property="email", type="string", example="1"),
     *  @OA\Property(property="gallery", type="array", @OA\Items(
     *  @OA\Property(property="image_gallery1", type="object", example="1"),
     *  @OA\Property(property="image_gallery2", type="object", example="1"),
     *  @OA\Property(property="image_gallery3", type="object", example="1"),
     *  @OA\Property(property="image_gallery4", type="object", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="latitude", type="string", example="1"),
     *  @OA\Property(property="longitude", type="string", example="1"),
     *  @OA\Property(property="destination", type="string", example="Street"),
     *  @OA\Property(property="sport", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="services", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="name", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="schedule", type="array", @OA\Items(
     *  @OA\Property(property="id", type="string", example="1"),
     *  @OA\Property(property="day", type="string", example="1"),
     *  @OA\Property(property="opening_time", type="string", example="1"),
     *  @OA\Property(property="closing_time", type="string", example="1"),
     *  ),
     *  ),
     *  @OA\Property(property="description", type="string", example="Description"),
     *  @OA\Property(property="price", type="decimal", example="1"),
     *  )
     *  )
     * 
     * @Rest\Get(path="/sportcenter/{id}/sport")
     * @Rest\View(serializerGroups={"sportcenter"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSportCenterSport(Request $request, $id)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $sportCenter = $entityManager->getRepository(SportCenter::class)->find($id);

        
            $sport = $sportCenter->getFkSport();
            $sportData = [];

            if ($sport === null){
                return new JsonResponse(
                    ['code' => 204, 'message' => 'No person found for this query.'],
                    Response::HTTP_NOT_FOUND
                );
            }

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

            $response = [
                'sport' => $sportData,
            ];

            return $this->json($response);
        

        
       
        
    }
   
}