<?php

namespace OCAX\Backup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * VaultSchedule
 *
 * @ORM\Table(name="vault_schedule")
 * @ORM\Entity
 */
class VaultSchedule
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
     * @var Vault
     *
     * @ORM\ManyToOne(targetEntity="Vault", inversedBy="schedules")
     */
    private $vault;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $day;




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
     * Set vault
     *
     * @param Vault $vault
     *
     * @return VaultSchedule
     */
    public function setVault($vault)
    {
        $this->vault = $vault;

        return $this;
    }

    /**
     * Get vault
     *
     * @return Vault
     */
    public function getVault()
    {
        return $this->vault;
    }

    /**
     * Set day
     *
     * @param \DateTime $day
     *
     * @return VaultSchedule
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return \DateTime
     */
    public function getDay()
    {
        return $this->day;
    }
}
