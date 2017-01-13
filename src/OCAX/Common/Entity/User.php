<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\OCM\Entity\Reply;
use OCAX\CMS\Entity\Archive;
use OCAX\CMS\Entity\Newsletter;
use OCAX\OCM\Entity\Enquiry;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="OCAX\Common\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     */
    private $plainpassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $email;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $joined;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $activationcode;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $member;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $descriptioneditor;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $teamMember;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $editor;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $manager;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\EnquiryEmail", mappedBy="sender")
     */
    private $emails;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\Enquiry", mappedBy="user")
     */
    private $enquiries;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\CMS\Entity\Newsletter", mappedBy="sender")
     */
    private $newsletters;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\Reply", mappedBy="user")
     */
    private $replies;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ResetPassword", mappedBy="user")
     */
    private $resetpassword;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\EnquirySubscribe", mappedBy="user")
     */
    private $subscriptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\Vote", mappedBy="user")
     */
    private $votes;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\BlockUser", mappedBy="blockinguser")
     */
    private $blockingusers;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\OCM\Entity\BlockUser", mappedBy="blockeduser")
     */
    private $blockedusers;


    public function __construct()
    {
        $now=new \DateTime();

        $this->active = true;
        $this->teammember = false;
        $this->joined=$now;
        $this->member = false;
        $this->admin = false;
        $this->disabled = false;
        $this->descriptioneditor = false;
        $this->manager = false;
        $this->editor = false;
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set plainpassword
     *
     * @param string $plainpassword
     *
     * @return User
     */
    public function setPlainPassword($plainpassword)
    {
        $this->plainpassword = $plainpassword;

        return $this;
    }

    /**
     * Get plainpassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainpassword;
    }

    /**
     * Set activationcode
     *
     * @param string $activationcode
     *
     * @return User
     */
    public function setActivationCode($activationcode)
    {
        $this->activationcode = $activationcode;

        return $this;
    }

    /**
     * Get activationcode
     *
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationcode;
    }

    /**
     * Set joined
     *
     * @param \DateTime $joined
     *
     * @return User
     */
    public function setJoined($joined)
    {
        $this->joined = $joined;

        return $this;
    }

    /**
     * Get joined
     *
     * @return \DateTime
     */
    public function getJoined()
    {
        return $this->joined;
    }

    /**
     * Set language
     *
     * @param Language $language
     *
     * @return User
     */
    public function setLanguage(Language $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     *
     * @return User
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set member
     *
     * @param boolean $member
     *
     * @return User
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Is member
     *
     * @return boolean
     */
    public function isMember()
    {
        return $this->member;
    }

    /**
     * Set descriptioneditor
     *
     * @param boolean $descriptioneditor
     *
     * @return User
     */
    public function setDescriptionEditor($active)
    {
        $this->descriptioneditor = $active;

        return $this;
    }

    /**
     * Is descriptioneditor
     *
     * @return boolean
     */
    public function isDescriptionEditor()
    {
        return $this->descriptioneditor;
    }

    /**
     * Set teamMember
     *
     * @param boolean $teamMember
     *
     * @return User
     */
    public function setTeamMember($active)
    {
        $this->teamMember = $active;

        return $this;
    }

    /**
     * Is teamMember
     *
     * @return boolean
     */
    public function isTeamMember()
    {
        return $this->teamMember;
    }

    /**
     * Set editor
     *
     * @param boolean $editor
     *
     * @return User
     */
    public function setEditor($active)
    {
        $this->editor = $active;

        return $this;
    }

    /**
     * Is editor
     *
     * @return boolean
     */
    public function isEditor()
    {
        return $this->editor;
    }

    /**
     * Set manager
     *
     * @param boolean $manager
     *
     * @return User
     */
    public function setManager($active)
    {
        $this->manager = $active;

        return $this;
    }

    /**
     * Is manager
     *
     * @return boolean
     */
    public function isManager()
    {
        return $this->manager;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     *
     * @return User
     */
    public function setAdmin($active)
    {
        $this->admin = $active;

        return $this;
    }

    /**
     * Is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        if ($this->isDisabled()) {
            $roles=false;
        } else {
            $roles=array('IS_FULLY_AUTHENTICATED');
            if ($this->isAdmin()) {
                $roles[]='ROLE_ADMIN';
            }
            if ($this->isManager()) {
                $roles[]='ROLE_TEAMMANAGER';
            }
            if ($this->isTeamMember()) {
                $roles[]='ROLE_TEAMMEMBER';
            }
            if ($this->isEditor()) {
                $role[]='ROLE_EDITOR';
            }
            if ($this->isDescriptionEditor()) {
                $role[]='ROLE_DESCRIPTIONEDITOR';
            }
        }

        return $roles;
    }

    /**
     * Get archives
     *
     * @return ArrayCollection
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * Get blockedusers
     *
     * @return ArrayCollection
     */
    public function getBlockedUsers()
    {
        return $this->blockedusers;
    }

    /**
     * Get blockingusers
     *
     * @return ArrayCollection
     */
    public function getBlockingUsers()
    {
        return $this->blockingusers;
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
     * Get newsletters
     *
     * @return ArrayCollection
     */
    public function getNewsletters()
    {
        return $this->newsletters;
    }

    /**
     * Get replies
     *
     * @return ArrayCollection
     */
    public function getReplies()
    {
        return $this->replies;
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

    public function __toString()
    {
        return $this->getUsername();
    }
}
