<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BudgetDetail
 *
 * @ORM\Table(name="budget_detail")
 * @ORM\Entity
 */
class BudgetDetail
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
     * @var BudgetDate
     *
     * @ORM\ManyToOne(targetEntity="BudgetDate", inversedBy="details")
     */
    private $date;

    /**
     * @var BudgetToken
     *
     * @ORM\ManyToOne(targetEntity="BudgetToken", inversedBy="details")
     */
    private $token;

    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $data;

    /**
     * @var BudgetStage
     *
     * @ORM\ManyToOne(targetEntity="BudgetStage", inversedBy="details")
     */
    private $stage;



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
     * @param BudgetDate $date
     *
     * @return Detail
     */
    public function setDate(BudgetDate $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return BudgetDate
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set token
     *
     * @param BudgetToken $token
     *
     * @return Detail
     */
    public function setToken(BudgetToken $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return BudgetToken
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set stage
     *
     * @param BudgetStage $stage
     *
     * @return Detail
     */
    public function setStage(BudgetStage $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return BudgetStage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set data
     *
     * @param decimal $data
     *
     * @return Detail
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return decimal
     */
    public function getData()
    {
        return $this->data;
    }
}
