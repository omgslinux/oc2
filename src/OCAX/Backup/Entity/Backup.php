<?php

namespace OCAX\Backup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Backup
 *
 * @ORM\Table(name="backup")
 * @ORM\Entity
 */
class Backup
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
     * @ORM\ManyToOne(targetEntity="Vault", inversedBy="backups")
     */
    private $vault;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

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
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $enddate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $filesize;

    /**
     * @var integer
     *
     * @ORM\Column(type="tinyint", length=1, options={"comment":"null=not_finished 0=failed 1=success"})
     */
    private $state;





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
     * @return Backup
     */
    public function setVault(Vault $vault)
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
     * Set filename
     *
     * @param string $filename
     *
     * @return Backup
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return Backup
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set creationdate
     *
     * @param \DateTime $creationdate
     *
     * @return Backup
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
     * Set startdate
     *
     * @param \DateTime $startdate
     *
     * @return Backup
     */
    public function setStartDate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     *
     * @return Backup
     */
    public function setEndDate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->enddate;
    }

    /**
     * Set filesize
     *
     * @param integer $filesize
     *
     * @return Backup
     */
    public function setFileSize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->filesize;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Backup
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
}
