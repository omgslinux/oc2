<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Asuntos
 *
 * @ORM\Table(name="language")
 * @ORM\Entity
 */
class Language
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
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $langcode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private $language;



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
     * Set code
     *
     * @param string $code
     *
     * @return Language
     */
    public function setLangCode($code)
    {
        $this->langcode = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getLangCode()
    {
        return $this->langcode;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Get intropagecontents
     *
     * @return ArrayCollection
     */
    public function getIntroPageContents()
    {
        return $this->intropagecontents;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getSitePageConents()
    {
        return $this->sitepagecontents;
    }


    public function __toString()
    {
        return $this->getLanguage();
    }
}
