<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class BotSingleton {

    public $secret = 1;
    public $user;
    public $em;


    public function __construct(EntityManager $em) {
        $this->em = $em;
        $bot = $em->getRepository('AppBundle:CleverBot')
            ->findOneById(1);
        $this->secret = $bot->getSecret();
        $this->user = $bot->getUser();
    }

    function getSecret() {
        return $this->secret;
    }


}