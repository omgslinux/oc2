<?php

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\User;

/**
 * BlockUser
 *
 * @ORM\Table(name="block_user")
 * @ORM\Entity
 */
class BlockUser
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="blockingusers")
     */
    private $blockinguser;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\User", inversedBy="blockedusers")
     */
    private $blockeduser;



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
     * @return BlockUser
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
     * Set blockeduser
     *
     * @param User $blockeduser
     *
     * @return BlockUser
     */
    public function setBlockedUser(User $user)
    {
        $this->blockeduser = $user;

        return $this;
    }

    /**
     * Get blockeduser
     *
     * @return User
     */
    public function getBlockedUser()
    {
        return $this->blockeduser;
    }
}
