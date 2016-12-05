<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EnquiryState
 *
 * @ORM\Table(name="enquirystates")
 * @ORM\Entity
 */
class EnquiryState
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
     * @ORM\Column(type="string", length=16)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Enquiry", mappedBy="state")
     */
    private $enquiries;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EmailTemplate", mappedBy="enquirystate")
     */
    private $emailtemplates;




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
     * Add enquiry
     *
     * @param string $enquiry
     *
     * @return EnquiryState
     */
    public function addEnquiry(Enquiry $enquiry)
    {
        $this->enquiries->add($enquiry);
        $enquiry->setEnquiry($this);

        return $this;
    }

    /**
     * Get enquiries
     *
     * @return string
     */
    public function getEnquiries()
    {
        return $this->enquiries;
    }

    /**
     * Add emailtemplate
     *
     * @param EmailTemplate $template
     *
     * @return EnquiryState
     */
    public function addEmailTemplate(EmailTemplate $template)
    {
        $this->emailtemplates->add($template);
        $template->setEmailTemplate($this);

        return $this;
    }

    /**
     * Get emailtemplates
     *
     * @return ArrayCollection
     */
    public function getEmailTemplates()
    {
        return $this->emailtemplates;
    }

    /**
     * Set related
     *
     * @param Enquiry $related
     *
     * @return EnquiryRelationship
     */
    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    /**
     * Get related
     *
     * @return Enquiry
     */
    public function getRelated()
    {
        return $this->related;
    }
}
