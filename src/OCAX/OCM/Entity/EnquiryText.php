<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;
use OCAX\OCM\Entity\Enquiry;
use OCAX\OCM\Entity\EnquiryText;

/**
 * EnquiryText
 *
 * @ORM\Table(name="enquiry_text")
 * @ORM\Entity
 */
class EnquiryText
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
     * @ORM\ManyToOne(targetEntity="Enquiry", inversedBy="texts")
     */
    private $enquiry;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $body;




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
     * @param Enquiry $enquiry
     *
     * @return EnquiryText
     */
    public function setEnquiry(Enquiry $enquiry)
    {
        $this->enquiry = $enquiry;

        return $this;
    }

    /**
     * Get enquiry
     *
     * @return EnquiryText
     */
    public function getEnquiry()
    {
        return $this->enquiry;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EnquiryText
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
     * @return EnquiryText
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



    public function __toString()
    {
        return $this->getSubject();
    }
}
