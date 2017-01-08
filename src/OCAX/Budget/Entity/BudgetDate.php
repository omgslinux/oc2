<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BudgetDate
 *
 * @ORM\Table(name="budget_date")
 * @ORM\Entity
 */
class BudgetDate
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
     * @var \Date
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetDetail", mappedBy="date")
     */
    private $details;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetService", mappedBy="date")
     */
    private $services;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetProgram", mappedBy="date")
     */
    private $programs;



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
     * Set date
     *
     * @param \Date $date
     *
     * @return BudgetDate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \Date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get details
     *
     * @return BudgetDetail
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Add detail
     *
     * @param BudgetDetail $detail
     *
     * @return BudgetDate
     */
    public function addBugetDetail(BudgetDetail $detail)
    {
        $this->details->add($detail);
        $detail->setDate($detail);

        return $this;
    }

    public function __toString()
    {
        return $this->getDate();
    }
}
