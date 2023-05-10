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

        // Add the `username` and `id` fields to the token payload
        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['username'] = $user->getUsername();
        $payload['name_and_lastname'] = $user->getNameAndLastname();
        
        $event->setData($payload);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJwtCreated',
        ];
    }
}
