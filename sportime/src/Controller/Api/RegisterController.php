<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenAPI\Annotations\Get;
use OpenAPI\Annotations\Items;
use OpenAPI\Annotations\JsonContent;
use OpenAPI\Annotations\Parameter;
use OpenAPI\Annotations\Response as OAResponse;
use OpenAPI\Annotations\Schema;
use OpenAPI\Annotations\Tag;



class RegisterController extends AbstractController{

    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        // ...
        $this->jwtEncoder = $jwtEncoder;
    }


   /**
    * register
    *
    * Register and get a new user 
    *
    * @OA\Tag(name="Register")
    *
    *  @OA\Response(
    *   response=200,
    *   description="Returns the new user"
    * )
    * @Route("/register", name="register", methods={"POST","GET"})
    */

public function registro(Request $request, UserPasswordEncoderInterface $encoder, JWTTokenManagerInterface $jwtManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $user = new User();
    
    $user->setEmail($data['email']);
    $user->setPassword($encoder->encodePassword($user, $data['password']));
    $user->setRoles(['ROLE_USER']);
    // Aquí puedes agregar otros campos de usuario, como nombre, apellido, etc.
    $user->setUsername($data['username']);
    //$person->setNameAndLastname($data['name_lastname']);
    $user->setPhone($data['phone']);
    

     //Verifica si ya existe un usuario con este correo electrónico en la base de datos
     $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);
     if ($existingUser) {
        $response = [
            'status' => 'error',
            'message' => 'Ya existe un usuario con este correo electrónico'
        ];
        return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
     }

     $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $data['username']]);
     if ($existingUser) {
        $response = [
            'status' => 'error',
            'message' => 'Ya existe el nombre de usuario'
        ];
        return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
     }

     $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['phone' => $data['phone']]);
     if ($existingUser) {
        $response = [
            'status' => 'error',
            'message' => 'Ya existe el número de telefono'
        ];
        return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
     }

    $entityManager = $this->getDoctrine()->getManager();
    //$entityManager->persist($person);
    $entityManager->persist($user);
    $entityManager->flush();

    $person= new Person();
    $person->setFkUser($user);
    $person->setNameAndLastname($data['name_and_lastname']);
    $person->setImageProfile('/images/profile/default.jpg');
    $person->setImageBanner('/images/banner/default.jpg');
    $entityManager->persist($person);
    $entityManager->flush();

    //get de las fotos de perfil con la url
    $getPhotoProfile = $person->getImageProfile();
    $photoProfile = $this->getParameter('url') . $getPhotoProfile;
    
    //get de las fotos de banner con la url
    $getPhotoBanner = $person->getImageBanner();
    $photoBanner = $this->getParameter('url') . $getPhotoBanner;

    $payload = [
        'email' => $user->getEmail(),
        'username' => $user->getUsername(),
        'name_and_lastname' => $person->getNameAndLastname(),
        'id' => $person->getId(),
        'image_profile' => $photoProfile,
        'image_banner' =>  $photoBanner,
    ];


    $token = $this->jwtEncoder->encode($payload);
  

    $response = [
        'status' => 'success',
        'message' => 'El usuario ha sido registrado exitosamente',
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
            'name_and_lastname' => $person->getNameAndLastname(), // 'name_lastname' es el nombre de la propiedad en la entidad 'Person
            'phone' => $user->getPhone(),
            'token' => $token,
            
            // ...
        ]
    ];

    return new JsonResponse($response, Response::HTTP_CREATED);
}

}
