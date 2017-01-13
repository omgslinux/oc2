<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * EnquiryEmail
 *
 * @ORM\Table(name="enquiry_email")
 * @ORM\Entity
 */
class EnquiryEmail
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
     * email generated by 0 = workflow, 1 = user
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $fromuser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="emails")
     */
    private $sender;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $sent;

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
     * @var Enquiry
     *
     * @ORM\ManyToOne(targetEntity="Enquiry", inversedBy="emails")
     */
    private $enquiry;


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


    public function __construct()
    {
        $this->creationdate=new \DateTime();
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
     * @return Email
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
     * Set enquiry
     *
     * @param Enquiry $enquiry
     *
     * @return Email
     */
    public function setEnquiry(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;

        return $this;
    }

    /**
     * Get enquiry
     *
     * @return Enquiry
     */
    public function getEnquiry()
    {
        return $this->enquiry;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return User
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
     * Set sent
     *
     * @param boolean $sent
     *
     * @return Email
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
     * @return Email
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
     * @return Email
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
     * Set subject
     *
     * @param string $subject
     *
     * @return Enquiry
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Enquiry
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
     * Get subscriptions
     *
     * @return ArrayCollection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }


    public function __toString()
    {
        return $this->getSubject();
    }
}
