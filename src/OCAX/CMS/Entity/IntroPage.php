<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * IntroPage
 *
 * @ORM\Table(name="intropage")
 * @ORM\Entity
 */
class IntroPage
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", length=2)
     */
    private $weight;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=3)
     */
    private $toppos;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=3)
     */
    private $leftpos;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=6)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=6)
     */
    private $bgcolor;

    /**
     * @var tinyint
     *
     * @ORM\Column(type="smallint")
     */
    private $opacity;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", length=4)
     */
    private $width;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $published;


    public function __construct()
    {
        $this->toppos = '50';
        $this->leftpos = '50';
        $this->color = '222222';
        $this->bgcolor = 'FFFFFF';
        $this->opacity = '8';
        $this->width = '600';
        $this->published = '0';
    }


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
     * Set weight
     *
     * @param tinyint $weight
     *
     * @return IntroPage
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return tinyint
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set toppos
     *
     * @param tinyint $toppos
     *
     * @return IntroPage
     */
    public function setTopPos($toppos)
    {
        $this->toppos = $toppos;

        return $this;
    }

    /**
     * Get toppos
     *
     * @return tinyint
     */
    public function getTopPos()
    {
        return $this->toppos;
    }

    /**
     * Set leftpos
     *
     * @param tinyint $leftpos
     *
     * @return IntroPage
     */
    public function setLeftPos($leftpos)
    {
        $this->leftpos = $leftpos;

        return $this;
    }

    /**
     * Get leftpos
     *
     * @return tinyint
     */
    public function getLeftPos()
    {
        return $this->leftpos;
    }

    /**
     * Set color
     *
     * @param varchar $color
     *
     * @return IntroPage
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return varchar
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set bgcolor
     *
     * @param varchar $bgcolor
     *
     * @return IntroPage
     */
    public function setBgColor($bgcolor)
    {
        $this->bgcolor = $bgcolor;

        return $this;
    }

    /**
     * Get bgcolor
     *
     * @return varchar
     */
    public function getBgColor()
    {
        return $this->bgcolor;
    }

    /**
     * Set opacity
     *
     * @param tinyint $opacity
     *
     * @return IntroPage
     */
    public function setOpacity($opacity)
    {
        $this->opacity = $opacity;

        return $this;
    }

    /**
     * Get opacity
     *
     * @return tinyint
     */
    public function getOpacity()
    {
        return $this->opacity;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return IntroPage
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return IntroPage
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Is published
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }
}
