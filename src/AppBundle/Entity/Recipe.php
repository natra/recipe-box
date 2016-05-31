<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Recipe
 *
 * @ORM\Table(name="recipe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecipeRepository")
 * @Vich\Uploadable
 */
class Recipe
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
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      max = 40,
     *      minMessage = "The recipe's title must be at least {{ limit }} characters long",
     *      maxMessage = "The recipe's title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "The recipe's description must be at least {{ limit }} characters long"
     * )
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Get id
     *
     * @return integer 
     */

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="recipes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Ingredient", mappedBy="recipe", cascade={"persist" })
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity="Direction", mappedBy="recipe", cascade={"persist"})
     */
    private $directions;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="recipe_image", fileNameProperty="imageName")
     * 
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @var string
     */
    private $imageName;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->updatedAt= new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt= new \DateTime();
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Recipe
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Recipe
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Recipe
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Recipe
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add ingredients
     *
     * @param \AppBundle\Entity\Ingredient $ingredients
     * @return Recipe
     */
    public function addIngredient(\AppBundle\Entity\Ingredient $ingredient)
    {
        
        //$this->ingredients[] = $ingredient;

        //return $this;

        $ingredient->setRecipe($this);
        $this->ingredients->add($ingredient);
    }

    /**
     * Remove ingredients
     *
     * @param \AppBundle\Entity\Ingredient $ingredients
     */
    public function removeIngredient(\AppBundle\Entity\Ingredient $ingredient)
    {
        $this->ingredients->removeElement($ingredient);
    }

    /**
     * Get ingredients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * Add directions
     *
     * @param \AppBundle\Entity\Direction $directions
     * @return Recipe
     */
    public function addDirection(\AppBundle\Entity\Direction $direction)
    {
        // $this->directions[] = $direction;

        // return $this;

        $direction->setRecipe($this);
        $this->directions->add($direction);
    }

    /**
     * Remove directions
     *
     * @param \AppBundle\Entity\Direction $directions
     */
    public function removeDirection(\AppBundle\Entity\Direction $direction)
    {
        $this->directions->removeElement($direction);
    }

    /**
     * Get directions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDirections()
    {
        return $this->directions;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Recipe
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $imageName
     *
     * @return Recipe
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }
}
