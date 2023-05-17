<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Person;
use Symfony\Component\HttpFoundation\Request;


class AddUserFieldsToTokenSubscriber implements EventSubscriberInterface
{
    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        /** @var UserInterface $user */
    
        $user = $event->getUser();

        // Obtener la instancia de Person asociada al objeto User
        $person = $user->getPerson();

        // Verificar si la instancia de Person es válida
        if ($person instanceof Person) {
            // Agregar la propiedad 'image_profile' al payload del token
            $payload['image_profile'] = $person->getImageProfile();
            $payload['name_and_lastname'] = $person->getNameAndLastname();
            $payload['id'] = $person->getId();
       
        }
        // Add the `username` and `id` fields to the token payload

        // $payload['id'] = $user->getId();
        $payload['username'] = $user->getUsername();
        $payload['email'] = $user->getEmail();

        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJwtCreated',
        ];
    }
}
