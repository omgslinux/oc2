<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;
use OCAX\Budget\Entity\BudgetToken;
use OCAX\OCM\Entity\Enquiry;

/**
 * Enquiry
 *
 * @ORM\Table(name="enquiry")
 * @ORM\Entity
 */
class Enquiry
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
     * @ORM\ManyToOne(targetEntity="Enquiry")
     */
    private $parent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="enquiries")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $teamMember;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $teamManager;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $modificationdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $assigndate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $submissiondate;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $registrynumber;

    /**
     *
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="File")
     */
    private $file;

    /**
     * generic=0, budgetary=1
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $budgetary;

    /**
     * addressed to the administration=0, addressed to the Observatory=1
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $addressedto;

    /**
     * @var EnquiryState
     *
     * @ORM\ManyToOne(targetEntity="EnquiryState", inversedBy="enquiries")
     */
    private $state;

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
     * @var BudgetToken
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Budget\Entity\BudgetToken", inversedBy="enquiries")
     */
    private $budget;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EnquiryEmail", mappedBy="enquiry")
     */
    private $emails;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EnquirySubscribe", mappedBy="enquiry")
     */
    private $subscriptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="EnquiryText", mappedBy="enquiry")
     */
    private $texts;


    public function __construct()
    {
        $this->setBudgetary(false);
        $this->registrynumber='_';
        $this->teamMember=0;
        $this->teamManager=0;
        $this->file=0;
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
     * Set user
     *
     * @param User $user
     *
     * @return Enquiry
     */
    public function setUser(User $user)
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
     * Set teamMember
     *
     * @param integer $teamMember
     *
     * @return Enquiry
     */
    public function setTeamMember($user)
    {
        $this->teamMember = $user;

        return $this;
    }

    /**
     * Get teamMember
     *
     * @return integer
     */
    public function getTeamMember()
    {
        return $this->teamMember;
    }

    /**
     * Set teamManager
     *
     * @param integer $teamManager
     *
     * @return Enquiry
     */
    public function setTeamManager($user)
    {
        $this->teamManager = $user;

        return $this;
    }

    /**
     * Get teamManager
     *
     * @return integer
     */
    public function getTeamManager()
    {
        return $this->teamManager;
    }

    /**
     * Set parent
     *
     * @param Enquiry $parent
     *
     * @return User
     */
    public function setRelated(Enquiry $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Enquiry
     */
    public function getParent()
    {
        return $this->parent;
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
     * Set modificationdate
     *
     * @param \DateTime $modificationdate
     *
     * @return User
     */
    public function setModificationDate($date)
    {
        $this->modificationdate = $date;

        return $this;
    }

    /**
     * Get modificationdate
     *
     * @return \DateTime
     */
    public function getModificationDate()
    {
        return $this->modificationdate;
    }

    /**
     * Set assigndate
     *
     * @param \DateTime $assigndate
     *
     * @return User
     */
    public function setAssignDate($date)
    {
        $this->assigndate = $date;

        return $this;
    }

    /**
     * Get assigndate
     *
     * @return \DateTime
     */
    public function getAssignDate()
    {
        return $this->assigndate;
    }

    /**
     * Set submissiondate
     *
     * @param \DateTime $submissiondate
     *
     * @return User
     */
    public function setSubmissionDate($date)
    {
        $this->submissiondate = $date;

        return $this;
    }

    /**
     * Get submissiondate
     *
     * @return \DateTime
     */
    public function getSubmissionDate()
    {
        return $this->submissiondate;
    }

    /**
     * Set registrynumber
     *
     * @param string $registrynumber
     *
     * @return User
     */
    public function setRegistryNumber($registrynumber)
    {
        $this->registrynumber = $registrynumber;

        return $this;
    }

    /**
     * Get registrynumber
     *
     * @return string
     */
    public function getRegistryNumber()
    {
        return $this->registrynumber;
    }

    /**
     * Set file
     *
     * @param File $file
     *
     * @return User
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set budgetary
     *
     * @param boolean $budgetary
     *
     * @return Enquiry
     */
    public function setBudgetary($active)
    {
        $this->budgetary = $active;
        if ($active===false) {
            $this->setAddressedTo(true);
        } else {
            $this->setAddressedTo(false);
        }

        return $this;
    }

    /**
     * Get budgetary
     *
     * @return boolean
     */
    public function isBudgetary()
    {
        return $this->budgetary;
    }

    /**
     * Set addressedto
     *
     * @param boolean $addressedto
     *
     * @return Enquiry
     */
    public function setAddressedTo($addressedto)
    {
        $this->addressedto = $addressedto;

        return $this;
    }

    /**
     * Get addressedto
     *
     * @return boolean
     */
    public function isAddressedTo()
    {
        return $this->addressedto;
    }

    /**
     * Set state
     *
     * @param EnquiryState $state
     *
     * @return Enquiry
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return EnquiryState
     */
    public function getState()
    {
        return $this->state;
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
     * Set budget
     *
     * @param BudgetToken $budget
     *
     * @return Enquiry
     */
    public function setBudget(BudgetToken $token)
    {
        $this->budget = $token;

        return $this;
    }

    /**
     * Get budget
     *
     * @return Enquiry
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Get emails
     *
     * @return ArrayCollection
     */
    public function getEmails()
    {
        return $this->emails;
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

    /**
     * Get texts
     *
     * @return ArrayCollection
     */
    public function getTexts()
    {
        return $this->texts;
    }

    public function getHumanType()
    {
        if ($this->isAddressedTo()) {
            return 'observatory';
        } else {
            return false;
        }
    }


    public function __toString()
    {
        return $this->getSubject();
    }
}
