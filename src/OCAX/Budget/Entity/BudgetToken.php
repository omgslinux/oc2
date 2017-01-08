<?php

namespace OCAX\Budget\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BudgetToken
 *
 * @ORM\Table(name="budget_token", uniqueConstraints={})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"service", "program", "economic"},
 *     message="There is already a service-program-economic in use on that host."
 * ) */
class BudgetToken
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
     * @ORM\ManyToOne(targetEntity="BudgetService", inversedBy="tokens")
     */
    private $service;

    /**
     * @var BudgetProgram
     *
     * @ORM\ManyToOne(targetEntity="BudgetProgram", inversedBy="tokens")
     */
    private $program;

    /**
     * @var BudgetEconomic
     *
     * @ORM\ManyToOne(targetEntity="BudgetEconomic", inversedBy="tokens")
     */
    private $economic;

    /**
     * @var Message
     *
     * @ORM\ManyToOne(targetEntity="OCAX\Common\Entity\Message")
     */
    private $message;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="BudgetDetail", mappedBy="token")
     */
    private $details;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\OCAX\OCM\Entity\Enquiry", mappedBy="budget")
     */
    private $enquiries;



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
     * Set service
     *
     * @param BudgetService $service
     *
     * @return BudgetToken
     */
    public function setService(BudgetService $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return BudgetService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set program
     *
     * @param BudgetProgram $program
     *
     * @return BudgetToken
     */
    public function setProgram(BudgetProgram $program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return BudgetProgram
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set economic
     *
     * @param BudgetEconomic $economic
     *
     * @return BudgetToken
     */
    public function setEconomic(BudgetEconomic $economic)
    {
        $this->economic = $economic;

        return $this;
    }

    /**
     * Get economic
     *
     * @return BudgetEconomic
     */
    public function getEconomic()
    {
        return $this->data;
    }

    /**
     * Set message
     *
     * @param Message $message
     *
     * @return BudgetToken
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->data;
    }
}
