<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;



class RegisterController extends AbstractController{


   /**
     *@Route("/register", name="register", methods={"POST","GET"})
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
    $user->setPhone($data['phone']);
    $token = $jwtManager->create($user);


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
    $entityManager->persist($user);
    $entityManager->flush();

    $response = [
        'status' => 'success',
        'message' => 'El usuario ha sido registrado exitosamente',
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
            
            'phone' => $user->getPhone(),
            'token' => $token,
            // ...
        ]
    ];

    return new JsonResponse($response, Response::HTTP_CREATED);
}

}
