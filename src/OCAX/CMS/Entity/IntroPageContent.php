<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;
use OCAX\Common\Entity\Language;

/**
 * IntroPageContent
 *
 * @ORM\Table(name="intro_page_content")
 * @ORM\Entity
 */
class IntroPageContent
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
     * @var IntroPage
     *
     * @ORM\ManyToOne(targetEntity="IntroPage", inversedBy="pages")
     */
    private $page;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="intropagecontents")
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $subtitle;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EnquirySubscribe", mappedBy="enquiry")
     */
//    private $subscriptions;



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
     * Set page
     *
     * @param IntroPage $page
     *
     * @return IntroPageContent
     */
    public function setPage(IntroPage $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return IntroPageContent
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set language
     *
     * @param Language $language
     *
     * @return IntroPageContent
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return IntroPageContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     *
     * @return IntroPageContent
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return IntroPageContent
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }


    public function __toString()
    {
        return $this->getTitle();
    }
}
