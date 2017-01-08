<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
 * Copyright (C) 2013-2016 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\OCM\Entity\EnquiryState;

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
     * @ORM\Column(type="string", length=32)
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
     * Set state
     *
     * @param string $state
     *
     * @return EnquiryState
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return EnquiryState
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

    public function __toString()
    {
        return $this->getDescription();
    }
}
