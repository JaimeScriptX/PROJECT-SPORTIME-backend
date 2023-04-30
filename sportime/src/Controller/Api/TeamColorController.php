<?php

namespace App\Controller\Api;


use App\Service\TeamColorManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class TeamColorController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/teamColor")
     * @Rest\View(serializerGroups={"teamColor"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(
        TeamColorManager $teamColorManager
    ) {
        return $teamColorManager->getRepository()->findAll();
        
    }
}