<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Translation
 *
 * @ORM\Table(name="translation", uniqueConstraints={@ORM\UniqueConstraint(name="IDX_translation", columns={"message_id", "language_id"})})
 * @ORM\Entity
 */
class Translation
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
     * @var Messages
     *
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="translations")
     */
    private $message;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(type="text")
     */
    private $translation;



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
     * Add language
     *
     * @param Language $language
     *
     * @return Translation
     */
    public function addLanguage(Language $language)
    {
        $this->languages->add($language);
        $language->setLanguage($this);

        return $this;
    }

    /**
     * Get langcodes
     *
     * @return ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Language
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __toString()
    {
        return $this->getDescription();
    }
}
