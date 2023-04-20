<?php

namespace App\Service;

use App\Entity\Person;
use App\Form\Model\PersonDto;
use App\Form\Type\PersonFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonFormProcessor
{

    private $personManager;
    private $formFactory;

    public function __construct(
        PersonManager $personManager,
        FormFactoryInterface $formFactory
    )
    {
        $this->personManager = $personManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Person $person, Request $request)
    {
       
    }
}