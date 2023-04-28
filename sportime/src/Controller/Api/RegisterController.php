<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RegisterController extends AbstractController{


   /**
     *@Route("/register", name="register", methods={"POST","GET"})
    */

public function registro(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $user = new User();
    $user->setEmail($data['email']);
    $user->setPassword($encoder->encodePassword($user, $data['password']));
    $user->setRoles(['ROLE_USER']);
    // Aquí puedes agregar otros campos de usuario, como nombre, apellido, etc.
    $user->setUsername($data['username']);
    $user->setNameAndLastname($data['name_and_lastname']);
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

     $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['name_and_lastname' => $data['name_and_lastname']]);
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
    ];

    return new JsonResponse($response, Response::HTTP_CREATED);
}

}
