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

            //obtiene el marcador 
            $idResult = $event->getId();
            $ResultEvents = $eventsResultsRepository->findBy(['fk_event' => $idResult]);

            foreach ($ResultEvents as $resultEvent) {

                $resultA = $resultEvent->getTeamA();
                $resultB = $resultEvent->getTeamB();
                
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
                'is_private' => $event->isIsPrivate(),
                'details' => $event->getDetails(),
                'price' => $event->getPrice(),
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
                'fk_teamcolor_id' => $event->getFkTeamColor() ? [
                    'id' => $event->getFkTeamColor()->getId(),
                    'colour' => $event->getFkTeamColor()->getColour(),
                    'image_shirt' => $shirt,
                ] : null,
                'state' => $event->getFkState() ? [
                    'id' => $event->getFkState()->getId(),
                    'type' => $event->getFkState()->getType(),
                    'colour' => $event->getFkState()->getColour(),
                ] : null,
                'fk_teamcolor_two_id' => $event->getFkTeamcolorTwo() ? [
                    'id' => $event->getFkTeamcolorTwo()->getId(),
                    'colour' => $event->getFkTeamcolorTwo()->getColour(),
                    'image_shirt' => $shirtTwo,
                ] : null,
                'events_results' => [
                    'team_a' => $resultA,
                    'team_b' => $resultB,
                ],
                'fk_person_id' => $event->getFkPerson() ? [
                    'id' => $event->getFkPerson()->getId(),
                    'image_profile' => $photoProfile,
                    'name_and_lastname' => $event->getFkPerson()->getNameAndLastname(),
                //    'birthday' => $event->getFkPerson()->getBirthday(),
                //    'weight' => $event->getFkPerson()->getWeight(),
                //    'height' => $event->getFkPerson()->getHeight(),
                //    'nationality' => $event->getFkPerson()->getNationality(),
                //    'fk_sex_id' => $event->getFkPerson()->getFkSex() ? [
                //        'id' => $event->getFkPerson()->getFkSex()->getId(),
                //        'gender' => $event->getFkPerson()->getFkSex()->getGender(),
                //    ] : null,
                    'fk_user_id' => [
                    //    'id' => $event->getFkPerson()->getFkUser()->getId(),
                    //    'email' => $event->getFkPerson()->getFkUser()->getEmail(),
                    //  'roles' => $event->getFkPerson()->getFkUser()->getRoles(),
                    //    'password' => $event->getFkPerson()->getFkUser()->getPassword(),
                        'username' => $event->getFkPerson()->getFkUser()->getUsername(),
                    //    'name_and_lastname' => $event->getFkPerson()->getFkUser()->getNameAndLastname(),
                    //    'phone' => $event->getFkPerson()->getFkUser()->getPhone(),
                    ],
                    
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
