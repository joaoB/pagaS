<?php

namespace AppBundle\Services;

use AppBundle\Entity\UserVote;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class Game {

    protected $request;
    public $em;

    public function __construct(EntityManager $em, RequestStack $request_stack) {
        $this->em = $em;
        $this->request = $request_stack->getCurrentRequest();
    }

    public function action($user) {

        $game = $this->em->getRepository('AppBundle:Game')->findAll();

        $data = [];
        $tableData = [];
        $OU = [];

        foreach ($game as $g) {

            $homeVotes = $g->getHomeWin();
            $awayVotes = $g->getAwayWin();
            $drawVotes = $g->getDraws();
            $totalVotes = $homeVotes + $awayVotes + $drawVotes;

            $homeP = intval(($homeVotes / $totalVotes) * 100);
            $awayP = intval(($awayVotes / $totalVotes) * 100);
            $drawP = 100 - $homeP - $awayP;

            $bigTotal = $totalVotes + $g->getOver() + $g->getUnder();

            $voteEntry = null;
            if ($user != null) {
                $voteEntry = $this->em->getRepository('AppBundle:UserVote')->findOneBy(Array("gameId" => $g->getId(), "userId" => $user->getId()));
            }

            $t = Array(
                "gameId" => $g->getId(),
                "game" => $g->getHome() . " vs " . $g->getAway(),
                "tip" => $this->getTip($g),
                "voteResult" => $voteEntry == null ? '' : $voteEntry->getResult(),
                "voteGoals" => $voteEntry == null ? '' : $voteEntry->getGoals(),
                "tipGoals" => $this->getTipGoals($g),
                "totalGameVotes" => $bigTotal,
                "tipPercentage" => max(Array($homeP, $awayP, $drawP))
            );

            $home = Array(
                "label" => $g->getHome(),
                "data" => $homeP
            );
            $away = Array(
                "label" => $g->getAway(),
                "data" => $awayP
            );
            $draw = Array(
                "label" => 'Empate',
                "data" => $drawP
            );
            array_push($tableData, $t);
            array_push($OU, $this->getOU($g));
            array_push($data, Array($home, $away, $draw));
        }

        return Array($data, $tableData, $OU);

    }

    public function newBet($user) {

        $r = $this->request;

        //validate this
        $gameId = $r->get('gameId');
        $betTip = $r->get('betTip');

        $game = $this->em->getRepository('AppBundle:Game')->findOneById($gameId);
        if (!$game) {
            //return error message, game does not exists
            return null;
        }

        $voted = $this->em->getRepository('AppBundle:UserVote')->findOneBy(Array("gameId" => $gameId, "userId" => $user->getId()));

        if ($voted == null) {
            $voted = new UserVote();
            $voted->setGameId($game);
            $voted->setUserId($user);
        }

        if (($voted->getResult() == null && $this->isResultBet($betTip)) || ($voted->getGoals() == null && $this->isGoalsBet($betTip))) {
            $this->updateAmountVotingOfGame($game, $betTip, +1);
        }

        if ($voted->getResult() != $betTip && $voted->getGoals() != $betTip) {
            $this->betType($betTip, $voted);
        }

        else {
            if ($betTip == $voted->getResult()) {
                $voted->setResult(null);
            }
            if ($betTip == $voted->getGoals()) {
                $voted->setGoals(null);
            }
            $this->updateAmountVotingOfGame($game, $betTip, -1);
        }


        $this->em->persist($voted);

        $this->em->flush();

        return 1;

    }

    private function isResultBet($betTip) {
        return $betTip == 'home' || $betTip == 'away' || $betTip == 'draw';
    }

    private function isGoalsBet($betTip) {
        return $betTip == 'under' || $betTip == 'over';
    }


    private function updateAmountVotingOfGame(\AppBundle\Entity\Game $game, $betTip, $amount) {
        if ($betTip == 'home') {
            $game->setHomeWin($game->getHomeWin() + $amount);
        }
        if ($betTip == 'away') {
            $game->setAwayWin($game->getAwayWin() + $amount);
        }
        if ($betTip == 'draw') {
            $game->setDraws($game->getDraws() + $amount);
        }
        if ($betTip == 'over') {
            $game->setOver($game->getOver() + $amount);
        }
        if ($betTip == 'under') {
            $game->setUnder($game->getUnder() + $amount);
        }

        $this->em->persist($game);
        $this->em->flush();
    }

    private function betType($betTip, UserVote $userVote) {
        if ($betTip == 'home' || $betTip == 'away' || $betTip == 'draw') {
            return $userVote->setResult($betTip);
        }
        if ($betTip == 'under' || $betTip == 'over') {
            return $userVote->setGoals($betTip);
        }

    }

    public function amountOfVotes() {
        $r = $this->request;
        //validate this
        $gameId = $r->get('gameId');

        $game = $this->em->getRepository('AppBundle:Game')->findOneById($gameId);
        if (!$game) {
            return null;
        }
        $h = $game->getHomeWin();
        $a = $game->getAwayWin();
        $d = $game->getDraws();
        $o = $game->getOver();
        $u = $game->getUnder();
        return $h + $a + $d + $o + $u;
    }

    private function getOU($g) {
        $over = $g->getOver();
        $under = $g->getUnder();
        $total = $over + $under;
        $over = intval(($over / $total) * 100);
        $under = intval(($under / $total) * 100);

        $over += ($over + $under < 100) ? abs(100 - $over - $under) : 0;

        return Array(Array("label" => "Mais", "data" => $over), Array("label" => "Menos", "data" => $under));

    }

    private function getTipGoals($g) {
        $votes = Array($g->getOver(), $g->getUnder());
        $goals = Array('Mais', 'Menos');
        $maxs = array_keys($votes, max($votes));
        return $goals[$maxs[0]];

    }

    private function getTip($g) {
        $votes = Array($g->getHomeWin(), $g->getAwayWin(), $g->getDraws());
        $teams = Array($g->getHome(), $g->getAway(), 'Empate');
        $maxs = array_keys($votes, max($votes));

        return $teams[$maxs[0]];

    }

}