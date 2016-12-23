<?php

namespace OCAX\Common\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Config
 *
 * @ORM\Table(name="config")
 * @ORM\Entity
 */
class Config
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
     * @var User
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $parameter;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=255)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $required;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="ConfigClass", inversedBy="parameterclasses")
     */
    private $parameterclass;




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
     * Set parameter
     *
     * @param string $parameter
     *
     * @return Config
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Config
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Config
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
     * Set required
     *
     * @param boolean $required
     *
     * @return Config
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Set parametertype
     *
     * @param string $parametertype
     *
     * @return Config
     */
    public function setParameterType($parametertype)
    {
        $this->parametertype = $parametertype;

        return $this;
    }

    /**
     * Get parametertype
     *
     * @return string
     */
    public function getParameterType()
    {
        return $this->parametertype;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
