<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SitePage
 *
 * @ORM\Table(name="site_page")
 * @ORM\Entity
 */
class SitePage
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
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $block;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $advancedHTML;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $showTitle;




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
     * Set block
     *
     * @param integer $block
     *
     * @return SitePage
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return integer
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set advancedHTML
     *
     * @param boolean $advancedHTML
     *
     * @return SitePage
     */
    public function setAdvancedHTML($advancedHTML)
    {
        $this->advancedHTML = $advancedHTML;

        return $this;
    }

    /**
     * Is advancedHTML
     *
     * @return boolean
     */
    public function isAdvancedHTML()
    {
        return $this->advancedHTML;
    }

    /**
     * Set showTitle
     *
     * @param boolean $showTitle
     *
     * @return SitePage
     */
    public function setShowTitle($showTitle)
    {
        $this->showTitle = $showTitle;

        return $this;
    }

    /**
     * Get showTitle
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return $this->showTitle;
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
