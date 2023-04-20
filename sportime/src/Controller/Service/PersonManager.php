<?php

namespace App\Service;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;

class PersonManager
{
    private $em;
    private $personRepository;

    public function __construct(EntityManagerInterface $em, PersonRepository $personRepository)
    {
        $this->em = $em;
        $this->personRepository = $personRepository;
    }

    public function find(int $id): ?Person
    {
        return $this->personRepository->find($id);
    }

    public function getRepository(): PersonRepository
    {
        return $this->personRepository;
    }

    public function create(): Person
    {
        $person = new Person();
        return $person;
    }

    public function save(Person $person): Person
    {
        $this->em->persist($person);
        $this->em->flush();
        return $person;
    }

    public function reload(Person $person): Person
    {
        $this->em->refresh($person);
        return $person;
    }

    public function delete(Person $person)
    {
        $this->em->remove($person);
        $this->em->flush();
    }
}