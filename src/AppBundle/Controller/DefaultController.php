<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller {
    /**
     * @Route("/aaa", name="homepage")
     */
    public function indexActionaa(Request $request) {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
        ));
    }

    /**
     * @Route("/chat", name="chatGet")
     * @Method("GET")
     */
    public function test() {
        $result = $this->get('game_worker')->a();
        return new Response(1);
    }


    /**
     * @Route("/chat", name="chatPost")
     * @Method("POST")
     */
    public function receiveChatCommand() {
        $result = $this->get('chat_worker')->action();
        return new JsonResponse($result);
    }


    /**
     * @Route("/message", name="submitMessage")
     * @Method("POST")
     */
    public function submitMessage() {
        $result = $this->get('message_worker')->action();
        return new Response($result);

    }

    /**
     * @Route("/t", name="graphs")
     */
    public function t() {

        //$data = Array( Array("label" => "Barca",  "data"=>25), Array("label" => "Real", "data"=>  75));
        //$data = Array( Array(Array("label" => "Barca",  "data"=>25), Array("label" => "Real", "data"=>  75)), Array(Array("label" => "Benfica",  "data"=>75), Array("label" => "sporting", "data"=>  75)));

        $data = $this->get('game_worker')->action();

        $html = $this->container->get('templating')->render(
            'lucky/index.html.twig',
            array('data' => $data)
        );


        return new Response($html);

    }


    /**
     * @Route("/", name="graphs")
     */
    public function indexAction() {
        $u = null;
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $u = $this->getUser();
        }

        $data = $this->get('game_worker')->action($u);
        //return new Response($data);
        $html = $this->container->get('templating')->render(
            'lucky/index.html.twig',
            array('data' => $data[0], 'table' => $data[1], 'OU' => $data[2], 'tomorrowGames' => $data[3], "user" => $u)
        );
        return new Response($html);
    }

    /**
     * @Route("/newBet", name="newBet")
     * @Method("POST")
     */
    public function newBet() {
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $this->get('game_worker')->newBet($this->getUser());
            $amountOfVotes = $this->get('game_worker')->amountOfVotes();
            return new JsonResponse(array('status' => $amountOfVotes));
        }
        else {
            return new JsonResponse(array('status' => 'needLogin'));
        }

    }


    /**
     * @Route("/amountOfVotes", name="amountOfVotes")
     * @Method("POST")
     */
    public function amountOfVotes() {
        $result = $this->get('game_worker')->amountOfVotes();
        return new Response($result);
    }

    /**
     * @Route("/history", name="historico")
     * @Method("GET")
     */
    public function historico() {
        $u = null;
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // authenticated (NON anonymous)
            $u = $this->getUser();
        }
        $result = $this->get('game_worker')->getPreviousGames();

        $html = $this->container->get('templating')->render(
            'lucky/historic.html.twig',
            array('history' => $result, "user" => $u)
        );
        return new Response($html);

    }

    //TODO: handle voting
    //vote with user validations
    //yesterday, today and tomorrow games


}
