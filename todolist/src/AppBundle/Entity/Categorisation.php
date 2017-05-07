<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * categorisation
 *
 * @ORM\Table(name="categorisation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\categorisationRepository")
 */
class Categorisation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="categorisations")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @var Todo
     *
     * @ORM\ManyToOne(targetEntity="Todo", inversedBy="categorisations")
     * @ORM\JoinColumn(name="todo_id", referencedColumnName="id", nullable=false)
     */
    private $todo;


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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Categorisation
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Categorisation
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set todo
     *
     * @param Todo $todo
     *
     * @return Categorisation
     */
    public function setTodo($todo)
    {
        $this->todo = $todo;

        return $this;
    }

    /**
     * Get todo
     *
     * @return Todo
     */
    public function getTodo()
    {
        return $this->todo;
    }
}

