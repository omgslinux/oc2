<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity
 */
class Message
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
     * @ORM\Column(type="text", unique=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="message")
     */
    private $translations;



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
     * Set message
     *
     * @param string $message
     *
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Language
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get translations
     *
     * @return string
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add budgettoken
     *
     * @param BudgetToken $budgettoken
     *
     * @return Message
     */
    public function addBudgetToken(BudgetToken $token)
    {
        $this->budgettokens->add($token);
        $token->setTokenMessage($this);

        return $this;
    }

    /**
     * Get budgettokens
     *
     * @return BudgetToken
     */
    public function getBudgetTokens()
    {
        return $this->budgettokens;
    }

    /**
     * Add budgetprogram
     *
     * @param BudgetProgram $budgetprogram
     *
     * @return Message
     */
    public function addBudgetProgram(BudgetProgram $budgetprogram)
    {
        $this->budgetprogram = $budgetprogram;

        return $this;
    }

    /**
     * Get budgetprograms
     *
     * @return BudgetProgram
     */
    public function getBudgetPrograms()
    {
        return $this->budgetprograms;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
