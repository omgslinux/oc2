<?php

namespace OCAX\CMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity
 */
class Newsletter
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishdate;

    /**
     * 0 = draft, 1 = failed, 2 = sent
     *
     * @var tinyint
     *
     * @ORM\Column(type="smallint")
     */
    private $sent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="newsletters")
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $sentas;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $recipients;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\EnquirySubscribe", mappedBy="enquiry")
     */
//    private $subscriptions;



    public function __construct()
    {
        $this->sent = 0;
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
     * Set sender
     *
     * @param User $sender
     *
     * @return Newsletter
     */
    public function setSender(User $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Newsletter
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
     * Set publishdate
     *
     * @param \DateTime $publishdate
     *
     * @return Newsletter
     */
    public function setPublishDate($date)
    {
        $this->publishdate = $date;

        return $this;
    }

    /**
     * Get publishdate
     *
     * @return \DateTime
     */
    public function getPublishDate()
    {
        return $this->publishdate;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     *
     * @return Newsletter
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Is sent
     *
     * @return boolean
     */
    public function isSent()
    {
        return $this->sent;
    }

    /**
     * Set sentas
     *
     * @param string $sentas
     *
     * @return Newsletter
     */
    public function setSentAs($sentas)
    {
        $this->sentas = $sentas;

        return $this;
    }

    /**
     * Get sentas
     *
     * @return string
     */
    public function getSentAs()
    {
        return $this->sentas;
    }

    /**
     * Set recipients
     *
     * @param string $recipients
     *
     * @return Newsletter
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Get recipients
     *
     * @return string
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Newsletter
     */
    public function setSubject($body)
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
        return $this->getSubject();
    }
}
