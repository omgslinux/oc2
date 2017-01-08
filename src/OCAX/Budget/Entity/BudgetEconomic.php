<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OCAX\Common\Entity\Message;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BudgetEconomic
 *
 * @ORM\Table(name="budget_economic", uniqueConstraints={})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"date_id", "economic"},
 *     message="There is already a date-economic in use on that host."
 * ) */
class BudgetEconomic
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
     * @var BudgetEconomic
     *
     * @ORM\ManyToOne(targetEntity="BudgetEconomic")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $spenditure;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $level;

    /**
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\Message")
     */
    private $message;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetToken", mappedBy="economic")
     */
    private $tokens;




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
     * Set parent
     *
     * @param BudgetEconomic $service
     *
     * @return BudgetEconomic
     */
    public function setParent(BudgetEconomic $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return BudgetEconomic
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set date
     *
     * @param BudgetDate $date
     *
     * @return BudgetEconomic
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
     * Set code
     *
     * @param string $code
     *
     * @return BudgetEconomic
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set spenditure
     *
     * @param boolean $spenditure
     *
     * @return BudgetEconomic
     */
    public function setSpenditure($spenditure)
    {
        $this->spenditure = $spenditure;

        return $this;
    }

    /**
     * Is spenditure
     *
     * @return boolean
     */
    public function isSpenditure()
    {
        return $this->spenditure;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return BudgetEconomic
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get messages
     *
     * @return ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add message
     *
     * @param Message $message
     *
     * @return BudgetEconomic
     */
    public function addMessage(Message $message)
    {
        $this->messages->add($message);
        $message->setMessage($this);

        return $this;
    }

    public function __toString()
    {
        return $this->getCode();
    }
}
