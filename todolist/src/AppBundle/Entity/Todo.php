<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Repository\RepositoryFactory;

/**
 * Todo
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TodoRepository")
 */
class Todo
{
    public $tags;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Categorisation", mappedBy="todo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $categorisations;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=255)
     */
    private $priority;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     */
    private $dueDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;


    /**
     * Get id
     *
     * @return int
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
     * @return Todo
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
     * Get category
     *
     * @return array
     */
    public function getCategories()
    {
        return array_map(function (Categorisation $categorisation) {
            return $categorisation->getCategory();
        },
            $this->categorisations->toArray()
        );
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Todo
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
     * Set priority
     *
     * @param string $priority
     *
     * @return Todo
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Todo
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Todo
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    function isPastDueDate()
    {
        return ($this->dueDate < new \DateTime());
    }

    /**
     * @param Categorisation $categorisation
     * @return Todo
     */
    public function addCategorisation(Categorisation $categorisation)
    {
        if (!$this->categorisations->contains($categorisation)) {
            $this->categorisations->add($categorisation);
            $categorisation->setTodo($this);
        }
        return $this;
    }

    /**
     * @param Categorisation $categorisation
     * @return Todo
     */
    public function removeCategorisation(Categorisation $categorisation)
    {
        if ($this->categorisations->contains($categorisation)) {
            $this->categorisations->remove($categorisation->getId());
            //$categorisation->setCategory(null);
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategorisations()
    {
        return $this->categorisations;
    }


}

