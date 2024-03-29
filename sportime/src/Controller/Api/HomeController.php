<?php

namespace App\Controller\Api;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\EventPlayersRepository;
use App\Repository\EventsRepository;
use App\Repository\EventsResultsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use DateInterval;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Events;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;



class HomeController extends AbstractFOSRestController
{
    /**
     * @OA\Tag(name="Home")
     * 
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->json($users);
    }

    /**
     * getEventsSportime
     * 
     * Get the last 7 events
     * 
     * @OA\Tag(name="Home")
     * 
     * @OA\Response(
     *   response=200,
     *  description="Returns the last 7 events"
     * )
     * 
     * 
     * @Rest\Get(path="/homeEvents")
     * @Rest\View(serializerGroups={"Events"}, serializerEnableMaxDepthChecks=true)
     */
    public function getEventsSportime(
        EventsRepository $eventsRepository,
        EntityManagerInterface $entityManager,
        EventPlayersRepository $eventPlayersRepository,
        EventsResultsRepository $eventsResultsRepository
    ) {
        $eventsRepository = $entityManager ->getRepository(Events::class);
        $events = $eventsRepository->findAll();

        if (!$events) {
            return new JsonResponse(
                ['code' => 204, 'message' => 'No events found for this query.'],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $data = [];
            //ordenar eventos por los que tengan la fecha y la hora más proxima a celebrarse y solo mostrar los 7 primeros y que no esten cancelados
            $events = $eventsRepository->findBy([], ['date' => 'ASC', 'time' => 'ASC']);
        foreach ($events as $event){
            $id = $event->getId();
            $eventPlayers = $eventPlayersRepository->findBy(['fk_event' => $id]);

            $eventPlayersA=[];
            $eventPlayersB=[];
            $allEventPlayers=[];
            
            $numParticipantes=0;
            foreach ($eventPlayers as $eventPlayer) {
                $numParticipantes++;
                
                

                $allEventPlayers[] = [
                    'fk_person_id' => $eventPlayer->getFkPerson()->getId(),

                ];

                if ($eventPlayer->getEquipo() == 1){

                     //get de las fotos de perfil con la url
                     $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
                     $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                    $eventPlayersA[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $photoProfile,
                    ];

                }else{
                    //get de las fotos de perfil con la url
                    $getPhotoProfile = $eventPlayer->getFkPerson()->getImageProfile();
                    $photoProfile = $this->getParameter('url') . $getPhotoProfile;

                    $eventPlayersB[] = [
                        'fk_person_id' => $eventPlayer->getFkPerson()->getId(),
                        'image_profile' => $photoProfile,
                    ];
                }
            }
            
            $duration = $event->getDuration();
            $hours = $duration->format('H');
            $minutes = $duration->format('i');

            $timeEnd = new \DateTime($event->getTime()->format('H:i'));
            $timeEnd->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));

             //get de las fotos de perfil con la url
             $getPhotoProfile = $event->getFkPerson()->getImageProfile();
             $photoProfile = $this->getParameter('url') . $getPhotoProfile;

              //get logo event
              $getLogoEvent = $event->getFkSport()->getLogoEvent();
              $LogoEvent = $this->getParameter('url') . $getLogoEvent;

              //get shirt
                $getShirt = $event->getFkTeamColor()->getImageShirt();
                $shirt = $this->getParameter('url') . $getShirt;
                
                $getShirtTwo = $event->getFkTeamColorTwo()->getImageShirt();
                $shirtTwo = $this->getParameter('url') . $getShirtTwo;
               //get imagesportcenter
                $fkSportCenter = $event->getFkSportcenter();
                $imageSportCenter = null;

                if ($fkSportCenter) {
                    $getImageSportCenter = $fkSportCenter->getImage();
                    $imageSportCenter = $this->getParameter('url') . $getImageSportCenter;
                }

                //if is_private 
            if ($event->isIsPrivate()==true){
                continue;
            }

            //if fk_state is 0b349f7f-0628-11ee-84aa-28e70f93b3c9
            if ($event->getFkState()->getId()=='0b349f7f-0628-11ee-84aa-28e70f93b3c9'){
                continue;
            }
            

            // fecha actual
            $dateNow = new \DateTime();
            $dateNow=$dateNow->format('Y-m-d');
            // fecha del evento
            $dateEvent = $event->getDate();
            $dateEvent=$dateEvent->format('Y-m-d');
            //hora actual +2 horas
            $timeNow = new \DateTime();
            $timeNow->add(new DateInterval('PT2H'));
            $timeNow=$timeNow->format('H:i');
            //hora del evento
            $timeEvent = $event->getTime();
            $timeEvent=$timeEvent->format('H:i');
            //si la fecha del evento es menor que la fecha actual
            if ($dateEvent < $dateNow) {
                continue;
            }
            //si la fecha del evento es igual que la fecha actual
            if ($dateEvent == $dateNow) {
                //si la hora del evento es menor que la hora actual
                if ($timeEvent < $timeNow) {
                    continue;
                }
            }
        
            //mostrar solo 7 eventos
            if (count($data) == 7) {
                break;
            }

            $data[] =[
                'id' => $event->getId(),
                'name' => $event->getName(),
                'details' => $event->getDetails(),
                'date' => $event->getDate()->format('d/m/Y'),
                'time' => $event->getTime()->format('H:i'),                
                'time_end' => $timeEnd->format('H:i'), // 'H:i:s
                'duration' => $event->getDuration()->format('H:i'),
                'number_players' => $event->getNumberPlayers(),
                'sport_center_custom' => $event->getSportCenterCustom(),
                'fk_sports_id' => $event->getFkSport() ?[
                    'id' => $event->getFkSport()->getId(),
                    'name' => $event->getFkSport()->getName(),
                    'logo_event' => $LogoEvent,
                ] : null,
                'fk_sportcenter_id' => $fkSportCenter ? [
                    'id' => $fkSportCenter->getId(),
                    'name' => $fkSportCenter->getName(),
                    'municipality' => $fkSportCenter->getMunicipality(),
                    'address' => $fkSportCenter->getAddress(),
                    'image' => $imageSportCenter,
                    'phone' => $fkSportCenter->getPhone(),
                    'latitude' => $fkSportCenter->getLatitude(),
                    'longitude' => $fkSportCenter->getLongitude(),
                    'destination' => $fkSportCenter->getDestination(),
                ] : null,
                'fk_difficulty_id' => $event->getFkDifficulty() ?[
                    'id' => $event->getFkDifficulty()->getId(),
                    'type' => $event->getFkDifficulty()->getType(),
                ] : null,
                'fk_sex_id' => $event->getFkSex() ? [
                    'id' => $event->getFkSex()->getId(),
                    'gender' => $event->getFkSex()->getGender(),
                ] : null,
                'event_players' => [
                    'event_players_A' => $eventPlayersA,
                    'event_players_B' => $eventPlayersB,
                ],

                
                    'event_players_list' => $allEventPlayers,
                

                'players_registered' => $numParticipantes,
                'missing_players' => $event->getNumberPlayers() *2 - $numParticipantes,
                

                
            ];
        }
        if (empty($data)) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'No hay eventos disponibles',
            ];
            return new JsonResponse($data, Response::HTTP_NOT_FOUND);
        } else {
        return new JsonResponse($data, Response::HTTP_OK);
        }
        }
    }
}
