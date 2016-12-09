<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * Vote
 *
 * @ORM\Table(name="vote")
 * @ORM\Entity
 */
class Vote
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
     * @var Reply
     *
     * @ORM\ManyToOne(targetEntity="Reply", inversedBy="votes")
     */
    private $reply;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="votes")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $like;





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
     * Set reply
     *
     * @param Reply $reply
     *
     * @return Vote
     */
    public function setReply(Reply $reply)
    {
        $this->reply = $reply;

        return $this;
    }

    /**
     * Get reply
     *
     * @return Reply
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Vote
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
     * Set like
     *
     * @param boolean $like
     *
     * @return Vote
     */
    public function setLike($like)
    {
        $this->like = $like;

        return $this;
    }

    /**
     * Is like
     *
     * @return boolean
     */
    public function isLike()
    {
        return $this->like;
    }
}
