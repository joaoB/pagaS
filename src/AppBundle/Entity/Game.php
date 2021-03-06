<?php
/**
 * Created by PhpStorm.
 * User: joasil
 * Date: 29-01-2016
 * Time: 14:01
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GameRepository")
 */
class Game {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="home", type="string", length=255)
     */
    private $home;

    /**
     * @var string
     *
     * @ORM\Column(name="away", type="string", length=255)
     */
    private $away;

    /**
     * @var integer
     *
     * @ORM\Column(name="homeWin", type="integer")
     */
    private $homeWin;

    /**
     * @var integer
     *
     * @ORM\Column(name="awayWin", type="integer")
     */
    private $awayWin;

    /**
     * @var integer
     *
     * @ORM\Column(name="draws", type="integer")
     */
    private $draws;

    /**
     * @var float
     *
     * @ORM\Column(name="goals", type="decimal", precision=8, scale=2)
     */
    private $goals;

    /**
     * @var integer
     *
     * @ORM\Column(name="over", type="integer")
     */
    private $over;


    /**
     * @var integer
     *
     * @ORM\Column(name="under", type="integer")
     */
    private $under;




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set home
     *
     * @param string $home
     *
     * @return Game
     */
    public function setHome($home)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get home
     *
     * @return string
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set away
     *
     * @param string $away
     *
     * @return Game
     */
    public function setAway($away)
    {
        $this->away = $away;

        return $this;
    }

    /**
     * Get away
     *
     * @return string
     */
    public function getAway()
    {
        return $this->away;
    }

    /**
     * Set homeWin
     *
     * @param integer $homeWin
     *
     * @return Game
     */
    public function setHomeWin($homeWin)
    {
        $this->homeWin = $homeWin;

        return $this;
    }

    /**
     * Get homeWin
     *
     * @return integer
     */
    public function getHomeWin()
    {
        return $this->homeWin;
    }

    /**
     * Set awayWin
     *
     * @param integer $awayWin
     *
     * @return Game
     */
    public function setAwayWin($awayWin)
    {
        $this->awayWin = $awayWin;

        return $this;
    }

    /**
     * Get awayWin
     *
     * @return integer
     */
    public function getAwayWin()
    {
        return $this->awayWin;
    }

    /**
     * Set draws
     *
     * @param integer $draws
     *
     * @return Game
     */
    public function setDraws($draws)
    {
        $this->draws = $draws;

        return $this;
    }

    /**
     * Get draws
     *
     * @return integer
     */
    public function getDraws()
    {
        return $this->draws;
    }

    /**
     * Set over
     *
     * @param integer $over
     *
     * @return Game
     */
    public function setOver($over)
    {
        $this->over = $over;

        return $this;
    }

    /**
     * Get over
     *
     * @return integer
     */
    public function getOver()
    {
        return $this->over;
    }

    /**
     * Set under
     *
     * @param integer $under
     *
     * @return Game
     */
    public function setUnder($under)
    {
        $this->under = $under;

        return $this;
    }

    /**
     * Get under
     *
     * @return integer
     */
    public function getUnder()
    {
        return $this->under;
    }

    /**
     * Set goals
     *
     * @param string $goals
     *
     * @return Game
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * Get goals
     *
     * @return string
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Game
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
