<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;
use OCAX\Common\Entity\Language;

/**
 * SitePageContent
 *
 * @ORM\Table(name="site_page_content")
 * @ORM\Entity
 */
class SitePageContent
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
     * @var SitePage
     *
     * @ORM\ManyToOne(targetEntity="IntroPage", inversedBy="pages")
     */
    private $page;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="sitepagecontents")
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $pageURL;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $pagetitle;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $previewbody;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $heading;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $metatitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $metadescription;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $metakeywords;




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
     * @param SitePage $page
     *
     * @return SitePageContent
     */
    public function setPage(SitePage $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return SitePageContent
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
     * @return SitePageContent
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
     * Set pageURL
     *
     * @param string $pageURL
     *
     * @return SitePageContent
     */
    public function setPageURL($url)
    {
        $this->pageURL = $url;

        return $this;
    }

    /**
     * Get pageURL
     *
     * @return string
     */
    public function getPageURL()
    {
        return $this->pageURL;
    }

    /**
     * Set pagetitle
     *
     * @param string $pagetitle
     *
     * @return SitePageContent
     */
    public function setPageTitle($title)
    {
        $this->pagetitle = $title;

        return $this;
    }

    /**
     * Get pagetitle
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pagetitle;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return SitePageContent
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

    /**
     * Set previewbody
     *
     * @param string $previewbody
     *
     * @return SitePageContent
     */
    public function setPreviewBody($previewbody)
    {
        $this->previewbody = $previewbody;

        return $this;
    }

    /**
     * Get previewbody
     *
     * @return string
     */
    public function getPreviewBody()
    {
        return $this->previewbody;
    }

    /**
     * Set heading
     *
     * @param string $heading
     *
     * @return SitePageContent
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get heading
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set metatitle
     *
     * @param string $metatitle
     *
     * @return SitePageContent
     */
    public function setMetaTitle($metatitle)
    {
        $this->metatitle = $metatitle;

        return $this;
    }

    /**
     * Get metatitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metatitle;
    }

    /**
     * Set metadescription
     *
     * @param string $metadescription
     *
     * @return SitePageContent
     */
    public function setMetaDescription($metadescription)
    {
        $this->metadescription = $metadescription;

        return $this;
    }

    /**
     * Get metadescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metadescription;
    }

    /**
     * Set metakeywords
     *
     * @param string $metakeywords
     *
     * @return SitePageContent
     */
    public function setMetaKeywords($metakeywords)
    {
        $this->metakeywords = $metakeywords;

        return $this;
    }

    /**
     * Get metakeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metakeywords;
    }


    public function __toString()
    {
        return $this->getTitle();
    }
}
