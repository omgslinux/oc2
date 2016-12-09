<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AppLog
 *
 * @ORM\Table(name="applog")
 * @ORM\Entity
 */
class AppLog
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $prefix;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $message;




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
     * Set user
     *
     * @param User $user
     *
     * @return Log
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Log
     */
    public function setCreationDate($creationdate)
    {
        $this->creationdate = $creationdate;

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
     * Set prefix
     *
     * @param string $prefix
     *
     * @return Log
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set model
     *
     * @param Model $model
     *
     * @return Log
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
