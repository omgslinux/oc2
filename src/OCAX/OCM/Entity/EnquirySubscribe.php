<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * EnquirySubscribe
 *
 * @ORM\Table(name="enquirysubscribe")
 * @ORM\Entity
 */
class EnquirySubscribe
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
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="subscriptions")
     */
    private $user;





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
     * @return EnquirySubscribe
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
     * @return EnquirySubscribe
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Enquiry
     */
    public function getUser()
    {
        return $this->user;
    }
}
