<?php
/**
 * Created by PhpStorm.
 * User: joasil
 * Date: 11-01-2016
 * Time: 15:29
 */

namespace AppBundle\Services;

use AppBundle\Entity\Email;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;


class Message {

    protected $request;
    protected $em;

    public function __construct(EntityManager $em, RequestStack $request_stack) {
        $this->em = $em;
        $this->request = $request_stack->getCurrentRequest();
    }


    public function action(){

        $r =  $this->request;
        $name = $r->get('name');
        $email = $r->get('email');
        $subject = $r->get('subject');
        $body = $r->get('body');

        $m = new Email();
        $m->setName($name);
        $m->setEmail($email);
        $m->setSubject($subject);
        $m->setMessage($body);
        $this->em->persist($m);
        $this->em->flush();

        return 1;
    }



}