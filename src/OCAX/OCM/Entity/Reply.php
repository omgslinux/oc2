<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * Reply
 *
 * @ORM\Table(name="reply")
 * @ORM\Entity
 */
class Reply
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
     * @var Enquiry
     *
     * @ORM\ManyToOne(targetEntity="Enquiry", inversedBy="subscriptions")
     */
    private $enquiry;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="replies")
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
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="reply")
     */
    private $votes;





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
     * Set enquiry
     *
     * @param string $enquiry
     *
     * @return Reply
     */
    public function setEnquiry(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;

        return $this;
    }

    /**
     * Get enquiry
     *
     * @return string
     */
    public function getEnquiry()
    {
        return $this->enquiry;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Reply
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Reply
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
     * @return Reply
     */
    public function setCreationDate($date)
    {
        $this->creationdate = $date;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return Reply
     */
    public function getCreationDate()
    {
        return $this->creationdate;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Reply
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return Reply
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get votes
     *
     * @return ArrayCollection
     */
    public function getVotes()
    {
        return $this->votes;
    }
}
