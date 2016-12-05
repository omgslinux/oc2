<?php

namespace OCM\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OCAXCommon\Entity\AppLog;

/**
 * Asuntos
 *
 * @ORM\Table(name="model")
 * @ORM\Entity
 */
class XModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="Id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(type="string", length=16)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="model")
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppLog", mappedBy="model")
     */
    private $logs;




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
     * Set description
     *
     * @param string $description
     *
     * @return Model
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
     * Get files
     *
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add file
     *
     * @param File $file
     *
     * @return Model
     */
    public function addFile(File $file)
    {
        $this->files->add($file);
        $file->setFile($this);

        return $this;
    }

    /**
     * Remove file
     *
     * @param File $file
     *
     * @return Model
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Model
     */
    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
        $comment->setComent($this);

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     *
     * @return Model
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * Get logs
     *
     * @return ArrayCollection
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Add log
     *
     * @param Log $log
     *
     * @return Model
     */
    public function addLog(Log $log)
    {
        $this->logs->add($log);
        $logs->setLog($this);

        return $this;
    }

    /**
     * Remove log
     *
     * @param Log $comment
     *
     * @return Model
     */
    public function removeLog(Log $log)
    {
        $this->logs->removeElement($log);

        return $this;
    }


    public function __toString()
    {
        return $this->getDescription();
    }
}
