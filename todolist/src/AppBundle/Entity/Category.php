<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Categorisation", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $categorisations;

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
     * @return Category
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
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return Category
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

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Category
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
     * @param Categorisation $categorisation
     * @return Category
     */
    public function addCategorisation(Categorisation $categorisation)
    {
        if (!$this->categorisations->contains($categorisation)) {
            $this->categorisations->add($categorisation);
            $categorisation->setCategory($this);
        }
        return $this;
    }

    /**
     * @param Categorisation $categorisation
     * @return Category
     */
    public function removeCategorisation(Categorisation $categorisation)
    {
        if ($this->categorisations->contains($categorisation)) {
            $this->categorisations->remove($categorisation);
            $categorisation->setCategory(null);
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

    /**
     * @return array
     */
    public function getTodos() {
        return array_map(function (Categorisation $categorisation) {
            return $categorisation->getTodo();
        },
            $this->categorisations->toArray()
        );
    }
}

