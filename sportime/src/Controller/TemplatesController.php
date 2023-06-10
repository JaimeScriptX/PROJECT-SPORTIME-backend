<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;


/**
 * @Route("/ddbb")
 */
class TemplatesController extends AbstractController
{
    /**
     * @Route("/database", name="app_database")
     */
    public function database()
    {   
        return $this->render('base.html.twig');
    }

}