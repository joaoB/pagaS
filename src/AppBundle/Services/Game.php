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

    public function a(){
        $game = $this->em->getRepository('AppBundle:Game')->getFutureGames();
        var_dump($game);
        return 1;
    }

    public function action($user) {

        $game = $this->em->getRepository('AppBundle:Game')->getFutureGames();

        $data = [];
        $tableData = [];
        $OU = [];
        $tomorrowGames = [];
        $date = new \DateTime();

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
                "tipPercentage" => max(Array($homeP, $awayP, $drawP)),
                "AmountOfGoals" => $g->getGoals() + 0
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

            $gameDay = $g->getDate();


            $date = date('Y-m-d', $gameDay->getTimestamp());
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('tomorrow'));
            $day_after_tomorrow = date('Y-m-d', strtotime('tomorrow + 1 day'));

            if ($date == $today) {
                array_push($tableData, $t);
            }
            else {
                if ($date == $tomorrow) {
                    array_push($tomorrowGames, $t);
                }
                else {
                    if ($date == $day_after_tomorrow) {

                    }
                }
            }

            $OU[$g->getId()] = $this->getOU($g);
            $data[$g->getId()] = Array($home, $away, $draw);
        }

        return Array($data, $tableData, $OU, $tomorrowGames);

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


    public function getPreviousGames() {
        $history = $this->em->getRepository('AppBundle:OldGames')->findAll();

        $historyResult = [];
        foreach ($history as $h) {
            $game = $this->em->getRepository('AppBundle:Game')->findOneById($h->getGame()->getId());

            $homeGoals = $h->getHomeResult();
            $awayGoals = $h->getAwayResult();
            $tipGoals = $this->getTipGoals($game); //return 'mais', 'menos'
            $wasTipGoalCorrect = $this->wasCorrectGoalsTip($game, $homeGoals + $awayGoals);
            $wasTipResultCorrect = $this->wasCorrectResultTip($game, $homeGoals, $awayGoals);
            $resultTip = $this->getTip($game);
            $entry = Array(
                "game" => $game->getHome() . " vs " . $game->getAway(),
                'homeGoals' => $homeGoals,
                'awayGoals' => $awayGoals,
                'tipGoals' => $tipGoals,
                'wasTipGoalCorrect' => $wasTipGoalCorrect,
                'resultTip' => $resultTip,
                'wasTipResultCorrect' => $wasTipResultCorrect);

            $historyResult[$game->getId()] = $entry;
        }

        return $historyResult;

    }

    private function wasCorrectResultTip(\AppBundle\Entity\Game $g, $home, $away) {
        if ($g->getHomeWin() > $g->getAwayWin() && $g->getHomeWin() > $g->getDraws() && $home > $away) {
            return true;
        }
        if ($g->getAwayWin() > $g->getHomeWin() && $g->getAwayWin() > $g->getDraws() && $away > $home) {
            return true;
        }
        if ($g->getDraws() > $g->getHomeWin() && $g->getDraws() > $g->getAwayWin() && $away == $home) {
            return true;
        }
        return false;

    }

    private function wasCorrectGoalsTip(\AppBundle\Entity\Game $g, $amountOfGoals) {
        if ($g->getOver() > $g->getUnder() && $amountOfGoals > $g->getGoals()) {
            //correct over tip
            return true;
        }
        if ($g->getUnder() > $g->getOver() && $amountOfGoals < $g->getGoals()) {
            return true;
        }

        return false; // incorrect
    }

}