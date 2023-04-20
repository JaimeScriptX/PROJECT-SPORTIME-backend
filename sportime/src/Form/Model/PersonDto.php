<?php

namespace App\Form\Model;

use App\Entity\Person;

class PersonDto{
    public $name;

    public function __constructor()
    {

    }

    public static function createFormPerson(Person $person): self
    {
        $dto = new self();
        $dto->name = $person->getName();
        return $dto;
    }
}