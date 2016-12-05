<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * Archive
 *
 * @ORM\Table(name="archive")
 * @ORM\Entity
 */
class Archive
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
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $container;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, options={"comment":"URI for files. Path name path for containers"})
     */
    private $path;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Archive
     *
     * @ORM\ManyToOne(targetEntity="Archive")
     */
    private $parent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;





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
     * Set author
     *
     * @param User $author
     *
     * @return Archive
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->sender;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Archive
     */
    public function setCreationDate($date)
    {
        $this->creationdate = $date;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationdate;
    }

    /**
     * Set container
     *
     * @param boolean $container
     *
     * @return Archive
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Is container
     *
     * @return boolean
     */
    public function isContainer()
    {
        return $this->container;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Archive
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Archive
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set parent
     *
     * @param Archive $parent
     *
     * @return Archive
     */
    public function setParent(Archive $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Archive
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Archive
     */
    public function setDescription($description)
    {
        $this->body = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->body;
    }


    public function __toString()
    {
        return $this->getSubject();
    }
}
