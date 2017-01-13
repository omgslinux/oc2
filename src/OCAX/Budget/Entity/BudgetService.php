<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use OCAX\Common\Entity\Message;

/**
 * BudgetService
 *
 * @ORM\Table(name="budget_service", uniqueConstraints={})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"date_id", "code"},
 *     message="There is already a date-code in use on that host."
 * ) */
class BudgetService
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
     * @var BudgetService
     *
     * @ORM\ManyToOne(targetEntity="BudgetService")
     */
    private $parent;

    /**
     * @var BudgetDate
     *
     * @ORM\ManyToOne(targetEntity="BudgetDate", inversedBy="services")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=2)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $level;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="OCAX\Common\Entity\Message")
     */
/*    private $message;
*/
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetToken", mappedBy="service")
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
     * @param BudgetService $service
     *
     * @return BudgetService
     */
    public function setParent(BudgetService $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return BudgetService
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
     * @return BudgetService
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
     * @return BudgetService
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
     * Set level
     *
     * @param integer $level
     *
     * @return BudgetService
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
     * @return BudgetService
     */
    public function addMessage(Message $message)
    {
        $this->messages->add($message);
        $message->setMessage($this);

        return $this;
    }
}
