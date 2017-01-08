<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BudgetStage
 *
 * @ORM\Table(name="budget_stage")
 * @ORM\Entity
 */
class BudgetStage
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
    private $stage;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetDetail", mappedBy="stage")
     */
    private $details;



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
     * Set stage
     *
     * @param string $stage
     *
     * @return BudgetStage
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Add detail
     *
     * @param BudgetDetail $detail
     *
     * @return BudgetStage
     */
    public function addDetail(BudgetDetail $detail)
    {
        $this->details->add($detail);
        $detail->setStage($this);

        return $this;
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

    public function __toString()
    {
        return $this->getStage();
    }
}
