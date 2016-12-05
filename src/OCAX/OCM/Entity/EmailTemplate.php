<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EmailTemplate
 *
 * @ORM\Table(name="email_template")
 * @ORM\Entity
 */
class EmailTemplate
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
     * @var EnquiryState
     *
     * @ORM\ManyToOne(targetEntity="EnquiryState", inversedBy="emailtemplates")
     */
    private $enquirystate;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $updated;



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
     * Set enquirystate
     *
     * @param EnquiryState $enquirystate
     *
     * @return EmailTemplate
     */
    public function setEnquiryState($enquirystate)
    {
        $this->enquirystate = $enquirystate;

        return $this;
    }

    /**
     * Get enquirystate
     *
     * @return EnquiryState
     */
    public function getEnquiryState()
    {
        return $this->enquirystate;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return EmailTemplate
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
     * Set updated
     *
     * @param boolean $updated
     *
     * @return EmailTemplate
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Is updated
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return $this->updated;
    }
}
