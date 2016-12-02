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
    private $infotype;
    
    /**
     * @var string
     */
    private $infotitle;
    
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
     * Set infotype
     *
     * @param string $infotype
     * @return Information
     */
    public function setInfotype($infotype)
    {
        $this->infotype = $infotype;
        
        return $this;
    }

    /**
     * Get infotype
     *
     * @return string 
     */
    public function getInfotype()
    {
        return $this->infotype;
    }
    
    /**
     * Set infotitle
     *
     * @param string $infotitle
     * @return Information
     */
    public function setInfotitle($infotitle)
    {
        $this->infotitle = $infotitle;
        
        return $this;
    }

    /**
     * Get infotitle
     *
     * @return string 
     */
    public function getInfotitle()
    {
        return $this->infotitle;
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