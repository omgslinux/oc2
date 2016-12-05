<?php

namespace OCAX\Backup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\OCM\Entity\Model;

/**
 * Vault
 *
 * @ORM\Table(name="vault")
 * @ORM\Entity
 */
class Vault
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
     * @ORM\Column(type="string", length=255)
     */
    private $host;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, options={"comment":"name of the directory where backups are kept"})
     */
    private $path;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"comment":"0 = copies are on LOCAL host, 1 = copies are on REMOTE host"})
     */
    private $remote;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, options={"comment":"which day(s) to make the copy seven digit char, starts on Monday 0000000"})
     */
    private $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", options={"comment":"number for backups made (historical stats)"})
     */
    private $count;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", length=2, options={"comment":"number for backups per safe"})
     */
    private $capacity;

    /**
     * @var integer
     *
     * @ORM\Column(type="tinyint", length=2)
     */
    private $state;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Backup", mappedBy="vault")
     */
    private $backups;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="VaultSchedule", mappedBy="vault")
     */
    private $schedules;




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
     * Set host
     *
     * @param string $host
     *
     * @return Vault
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return Vault
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Vault
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return Vault
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     *
     * @return Vault
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Vault
     */
    public function setCreationDate($creationdate)
    {
        $this->creationdate = $creationdate;

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
     * Set count
     *
     * @param integer $count
     *
     * @return Vault
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Vault
     */
    public function setCapaity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Vault
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get backups
     *
     * @return ArrayCollection
     */
    public function getBackups()
    {
        return $this->backups;
    }

    /**
     * Add backup
     *
     * @param Backup $backup
     *
     * @return Vault
     */
    public function addBackup(Backup $backup)
    {
        $this->backups->add($backup);
        $backup->setBackup($this);

        return $this;
    }

    /**
     * Remove backup
     *
     * @param Backup $backup
     *
     * @return Vault
     */
    public function removeBackup(Backup $backup)
    {
        $this->backups->removeElement($backup);

        return $this;
    }

    /**
     * Get schedules
     *
     * @return ArrayCollection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add schedule
     *
     * @param VaultSchedule $schedule
     *
     * @return Vault
     */
    public function addSchedule(VaultSchedule $schedule)
    {
        $this->schedules->add($schedule);
        $schedule->setSchedule($this);

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param VaultSchedule $schedule
     *
     * @return Vault
     */
    public function removeSchedule(VaultSchedule $schedule)
    {
        $this->schedules->removeElement($schedule);

        return $this;
    }
}
