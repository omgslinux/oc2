<?php

/**
 * OCAX -- Citizen driven Municipal Observatory software
 * Copyright (C) 2013-2016 OCAX Contributors. See AUTHORS.

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCAX\OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File
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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    private $model;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $modelid;




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
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set model
     *
     * @param string $model
     *
     * @return File
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set modelid
     *
     * @param integer $modelid
     *
     * @return File
     */
    public function setModelId($modelid)
    {
        $this->modelid = $modelid;

        return $this;
    }

    /**
     * Get modelid
     *
     * @return Model
     */
    public function getModelId()
    {
        return $this->modelid;
    }

    /**
     * Get documents
     *
     * @return ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}
