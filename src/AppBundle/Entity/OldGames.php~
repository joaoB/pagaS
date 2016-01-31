<?php
/**
 * Created by PhpStorm.
 * User: joasil
 * Date: 31-01-2016
 * Time: 21:18
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="old_games")
 */
class OldGames {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game", referencedColumnName="id")
     */
    private $game;

    /**
     * @var integer
     *
     * @ORM\Column(name="homeResult", type="integer")
     */
    private $homeResult;

    /**
     * @var integer
     *
     * @ORM\Column(name="awayResult", type="integer")
     */
    private $awayResult;

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
     * Set homeResult
     *
     * @param integer $homeResult
     *
     * @return OldGames
     */
    public function setHomeResult($homeResult)
    {
        $this->homeResult = $homeResult;

        return $this;
    }

    /**
     * Get homeResult
     *
     * @return integer
     */
    public function getHomeResult()
    {
        return $this->homeResult;
    }

    /**
     * Set awayResult
     *
     * @param integer $awayResult
     *
     * @return OldGames
     */
    public function setAwayResult($awayResult)
    {
        $this->awayResult = $awayResult;

        return $this;
    }

    /**
     * Get awayResult
     *
     * @return integer
     */
    public function getAwayResult()
    {
        return $this->awayResult;
    }

    /**
     * Set game
     *
     * @param \AppBundle\Entity\game $game
     *
     * @return OldGames
     */
    public function setGame(\AppBundle\Entity\game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \AppBundle\Entity\game
     */
    public function getGame()
    {
        return $this->game;
    }
}
