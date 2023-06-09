<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;


class ForgotPasswordController extends AbstractController
{
    /**
     * @Route("/forgot-password", name="forgot_password", methods={"POST"})
     */
    public function forgotPassword(
        Request $request,
        MailerInterface $mailer
    ) {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
    
        // Buscar al usuario por su email
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
    
        // Verificar si el usuario existe
        if (!$user) {
            // El usuario no existe, puedes devolver una respuesta de error o simplemente decir que se envió el correo
            return new Response('Se ha enviado un correo electrónico si la dirección proporcionada existe.');
        }
    
        // Obtener la contraseña actual del usuario
        $currentPassword = $user->getPassword();
    
        $this->getDoctrine()->getManager()->flush();
    
        // Crear y enviar el correo electrónico
        $email = (new Email())
            ->from('noreply@tu-domino.com')
            ->to($email)
            ->subject('Recuperación de contraseña')
            ->html('<p>Tu contraseña actual es: ' . $currentPassword . '</p>');
        
        $mailer->send($email);
    
        return new Response('Se ha enviado un correo electrónico con la contraseña actual.');
    }
}
