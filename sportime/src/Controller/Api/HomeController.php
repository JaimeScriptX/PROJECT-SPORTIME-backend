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




class HomeController extends AbstractFOSRestController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->json($users);
    }

    /**
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
            //ordenar eventos por id descendente y solo mostrar los 7 primeros y que no esten cancelados
            $events = $eventsRepository->findBy([], ['id' => 'DESC'], 7);
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
        return new JsonResponse($data, Response::HTTP_OK);

        }
        
    }
}
