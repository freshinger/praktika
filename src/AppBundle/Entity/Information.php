<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Information
 */
class Information
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var \DateTime
     */
    private $datum;
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $content;
    
    /**
     * @var \User
     */
    private $user;
    
    /**
     * @var \Firma
     */
    private $firma;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __construct()
    {
        $this->datum = new \DateTime();
    }
    
    /**
     * Set datum
     *
     * @param \DateTime $datum
     * @return Information
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;
        
        return $this;
    }

    /**
     * Get datum
     *
     * @return \DateTime 
     */
    public function getDatum()
    {
        return $this->datum;
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return Information
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Information
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
        return $this->type;
    }
    
    /**
     * Set content
     *
     * @param string $content
     * @return Content
     */
    public function setContent($content)
    {
        $this->content = $content;
        
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Set user
     */
    public function setUser(\Appbundle\Entity\User $user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    /**
     * Get user
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set firma
     */
    public function setFirma(\Appbundle\Entity\Firma $firma)
    {
        $this->firma = $firma;
        
        return $this;
    }
    
    /**
     * Get firma
     */
    public function getFirma()
    {
        return $this->firma;
    }
}